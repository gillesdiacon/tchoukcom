<?php

use TcBern\Model\Info;
use TcBern\Model\User;
use TcBern\Model\Group;
use TcBern\Model\Profile;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$slimSettings = array('determineRouteBeforeAppMiddleware' => true);
$slimConfig = array('settings' => $slimSettings);
$app = new \Slim\App(['settings' => ['determineRouteBeforeAppMiddleware' => true, 'displayErrorDetails' => true]]);

$c = $app->getContainer();
//$c['errorHandler'] = function ($c) {
//    return function ($request, $response, $exception) use ($c) {
//        $response->getBody()->rewind();
//        return $c['response']->withStatus(500)
//                             ->withHeader('X-Status-Reason', $exception->getMessage())
//                             ->withHeader('Content-Type', 'text/html')
//                             ->write('Something went wrong!');
//    };
//};

//$app->add(new \Slim\Middleware\JwtAuthentication([
//    "secure" => false,
//    "path" => "/api",
//    "secret" => "supersecretkeyyoushouldnotcommittogithub"
//]));
$cipher = mcrypt_module_open('rijndael-256', '', 'ofb', '');
$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_DEV_RANDOM);
$keySize = mcrypt_enc_get_key_size($cipher);
$encryptionKey = substr(md5('supersecretkeyyoushouldnotcommittogithub'), 0, $keySize);

function tokenFunc(Request $request, Response $response, $next) {
    if (containsNoToken($request)) {
        $response->getBody()->write("Authentication is required but no token found");
        return $response->withStatus(401);
    } else {
        return $next($request, $response);
    }
};

$tokenMw = function(Request $request, Response $response, $next) {
    return tokenFunc($request, $response, $next);
};

$authenticationMw = function(Request $request, Response $response, $next) {
    $route = $request->getAttribute('route');
    $entity = current($route->getArguments());

    if (isAuthenticationRequired($entity)) {
        return tokenFunc($request, $response, $next);
    } else {
        return $next($request, $response);
    }
};

$verificationMw = function($request, $response, $next) {
    $route = $request->getAttribute('route');
    $entity = current($route->getArguments());

    if (verifyEntityParam($entity)) {
        return $next($request, $response);
    } else {
        $response->getBody()->write("Entity '$entity' not found");
        return $response->withStatus(404);
    }
};

$headerMw = function($request, $response, $next) {
    $response = $next($request, $response);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*');
};

// list of authorized entities
$authorizedEntities = array(
    "shopcategories" => "TcBern\\Shop\\ShopCategory",
    "internationalisation" => "TcBern\\Model\\Internationalisation",
    "identities" => "TcBern\\Model\\Identity",
    "users" => "TcBern\\Model\\User");

// list of entities that requires an authentication
$authenticationRequiredEntities = array(
    "identities" => "TcBern\\Model\\Identity",
    "users" => "TcBern\\Model\\User");

// list of entities that requires the ownership to be modified
$ownershipRequiredEntities = array(
    "identities" => "TcBern\\Model\\Identity",
    "users" => "TcBern\\Model\\User");

$app->get(
    '/',
    function () use ($app) {
        echo "Welcome to API -> PHP Version " . phpversion();
    }
);

$app->options(
    '/*',
    function (Request $request, Response $response) {
        $response()->header('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        $response()->header('Access-Control-Allow-Origin', '*');
        $response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
);

$app->post(
    '/auth',
    function (Request $request, Response $response) {
        global $cipher, $encryptionKey, $iv;
        $params = $request->getParsedBody();

        $username = $params['username'];
        $password = $params['password'];
        $user = User::where('username', '=', $username)->first();

        if ($user != null && $user->password == $params['password']) {
            $token = array(
                "id" => $user->id,
                "exp" => time() + (60 * 60 * 24)
            );
            $encoded = encode($token, $cipher, $encryptionKey, $iv);
            return $response->getBody()->write(
                json_encode(array("token" => $encoded, "userId" => $user->id, "group" => array_map('mapGroups', $user->groups()->get()->all()))));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Username or password incorrect")));
            return $response->withStatus(503);
        }
    }
)->add($headerMw);

$app->post(
    '/password/{id}',
    function (Request $request, Response $response, $args) {
        global $authorizedEntities, $cipher, $encryptionKey, $iv;
        
        $id = $args['id'];
        $token = $request->getHeaderLine('Token');
        $decoded = decode($token, $cipher, $encryptionKey, $iv);

        if (/* check admin rights */ isOwnershipRequired('users') && isNotOwner('users', $id, $decoded->id)) {
            $response->getBody()->write("You must be the owner of these data");
            return $response->withStatus(401);
        }

        $input = $request->getParsedBody();
        $user = User::find($id);

        if ($user) {
            $user->password = $input['password'];
            $user->save();

            $response->getBody()->write("success");
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }
)->add($tokenMw);



$app->get(
    '/api/shopcategories',
    function(Request $request, Response $response, $args) {

		$languageId = 2;
        $rootCategoryId = 10;
        $categories = TcBern\Model\Category::
		where('parent_id', $rootCategoryId)
		->with(
			array('subCategories.title' => function($query) use ($languageId) {
					$query->where('language_id', $languageId);
				},
				'title' => function($query) use ($languageId) {
					$query->where('language_id', $languageId);
				}
			)
		)
		->get();

        $response->getBody()->write($categories->toJson());
        return $response;
    }
)->add($headerMw);



$app->get(
    '/api/committee',
    function(Request $request, Response $response) {
        $profiles = Profile::whereHas('positions', function($q) {
          $q->where('key', '=', 'position.committee');
        })->get();
        $response->getBody()->write($profiles->load('identity', 'positions')->toJson());
        return $response;
    }
)->add($headerMw);

$app->get(
    '/api/infopreview',
    function(Request $request, Response $response) {
        $response->getBody()->write(Info::orderBy('date', 'DESC')->take(3)->get()->toJson());
        return $response;
    }
)->add($headerMw);

// handle GET requests for /entity
$app->get(
    '/api/{entity}',
    function (Request $request, Response $response, $args) {
        global $authorizedEntities;

        $entity = $args['entity'];
        $objects = $authorizedEntities[$entity]::all();
        $response->getBody()->write($objects->toJson());
        
        return $response;
    }
)->add($authenticationMw)->add($verificationMw)->add($headerMw);

// handle GET requests for /entity/:id
$app->get(
    '/api/{entity}/{id}',
    function (Request $request, Response $response, $args) {
        global $authorizedEntities;

        $entity = $args['entity'];
        $id = $args['id'];
        $object = $authorizedEntities[$entity]::find($id);

        if ($object) {
            $response->getBody()->write($object->toJson());
            return $response;
        } else {
            return $response->withStatus(404);
        }
    }
)->add($authenticationMw)->add($verificationMw)->add($headerMw);;

// handle POST requests to /entity
$app->post(
    '/api/{entity}',
    function (Request $request, Response $response, $args) {
        global $authorizedEntities;

        $body = $request->getBody();
        $input = json_decode($body);
        $entity = $args['entity'];

        $object = new $authorizedEntities[$entity]();
        foreach($input as $key => $value) {
            $object->$key = (string)$value;
        }
        $object->save();

        $response->getBody()->write($object->toJson());
        return $response;
    }
)->add($tokenMw)->add($verificationMw)->add($headerMw);;

// handle PUT requests to /entity/:id
$app->put(
    '/api/{entity}/{id}',
    function (Request $request, Response $response, $args) {
        global $authorizedEntities, $cipher, $encryptionKey, $iv;

        $entity = $args['entity'];
        $id = $args['id'];

        $token = $request->getHeaderLine('Token');
        $decoded = decode($token, $cipher, $encryptionKey, $iv);
        if (/* check admin rights */ isOwnershipRequired($entity) && isNotOwner($entity, $id, $decoded->id)) {
            $app->halt(401, "You must be the owner of these data");
        }

        $body = $request->getBody();
        $input = json_decode($body);

        $object = $authorizedEntities[$entity]::find($id);

        // store modified article
        if ($object) {
            foreach($input as $key => $value) {
                $object->$key = (string)$value;
            }
            $object->save();

            $response->getBody()->write($object->toJson());
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }
)->add($tokenMw)->add($verificationMw)->add($headerMw);;

// handle DELETE requests for /entity/:id
$app->delete(
    '/api/{entity}/{id}',
    function (Request $request, Response $response, $args) {
        global $authorizedEntities, $cipher, $encryptionKey, $iv;
        
        $entity = $args['entity'];
        $id = $args['id'];

        $object = $authorizedEntities[$entity]::find($id);

        if ($object) {
            $object->delete();
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }
)->add($authenticationMw)->add($verificationMw)->add($headerMw);;

$app->run();

function mapGroups($item) {
    return array("id" => $item->id, "key" => $item->key);
}

function encode($token, $cipher, $encryptionKey, $iv) {
    /*mcrypt_generic_init($cipher, $encryptionKey, $iv);
    $encrypted = mcrypt_generic($cipher, json_encode($token));
    mcrypt_generic_deinit($cipher);*/
    return base64_encode(json_encode($token));
}

function decode($token, $cipher, $encryptionKey, $iv) {
    $decodedToken = base64_decode($token);
    /*mcrypt_generic_init($cipher, $encryptionKey, $iv);
    $decrypted = mdecrypt_generic($cipher, $decodedToken);
    mcrypt_generic_deinit($cipher);
    echo $decrypted;*/
    return json_decode($decodedToken);
}

function containsNoToken($request) {
    return $request->getHeader('Token') == null;
}

function isNotOwner($entity, $id, $userId) {
    return $id != $userId;
}

/**
 * Returns true if the entity parameter is part of
 * the listed of authorized entities
 *
 * @param $entityParam string the entity param
 * @return bool true if the entity param is authorized
 */
function verifyEntityParam($entityParam) {
    global $authorizedEntities, $cipher, $encryptionKey, $iv;

    return array_key_exists($entityParam, $authorizedEntities);
}

/**
 * Returns true if the entity parameter is part of
 * the listed of authentiation-required entities
 *
 * @param $entityParam string the entity param
 * @return bool true if the entity param is authentication-required
 */
function isAuthenticationRequired($entityParam) {
    global $authenticationRequiredEntities;

    return array_key_exists($entityParam, $authenticationRequiredEntities);
}

/**
 * Returns true if the entity parameter is part of
 * the listed of ownership-required entities
 *
 * @param $entityParam string the entity param
 * @return bool true if the entity param is ownership-required
 */
function isOwnershipRequired($entityParam) {
    global $ownershipRequiredEntities;

    return array_key_exists($entityParam, $ownershipRequiredEntities);
}

/**
 * Returns the class name corresponding to the entity param
 *
 * @param $entityParam string the entity param name
 *
 * @return mixed the entity class name
 */
function getEntityClassName($entityParam) {
    global $authorizedEntities, $cipher, $encryptionKey, $iv;

    return $authorizedEntities[$entityParam];
}
