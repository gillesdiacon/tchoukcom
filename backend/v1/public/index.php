<?php

use TcBern\Model\Info;
use TcBern\Model\User;
use TcBern\Model\Group;
use TcBern\Model\Profile;

use Illuminate\Database\Capsule\Manager as Capsule;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../private/dbConfig.php';

$slimSettings = array('determineRouteBeforeAppMiddleware' => true);
$slimConfig = array(
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => true,
    'db' => $dbConfig
);

$app = new \Slim\App(['settings' => $slimConfig]);

$container = $app->getContainer();

// Configure Eloquent
$capsule = new Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['db']->connection()->enableQueryLog();

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
    "categories" => "TcBern\\Model\\Category",
    "products" => "TcBern\\Model\\Product");
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




$app->get(
    '/api/shopcategory/{categoryId}',
    function(Request $request, Response $response, $args) {

        global $languageId;
        $languageId = 2;
        $categoryId = $args['categoryId'];

        $category = TcBern\Model\Category::find($categoryId);
        
        postConstructCategory($category);
        
        $response->getBody()->write($category->toJson());
        return $response;
    }
)->add($headerMw);

function postConstructCategory($category){
    
    $category->makeHidden('simpleProducts');
    $category->makeHidden('variantProducts');
    
    $subCategories = $category->getAttribute('subCategories');
    $nbSubCategories = count($subCategories);
    
    $nbProducts = count($category->getAttribute('simpleProducts')) + count($category->getAttribute('variantProducts'));
    if($nbSubCategories > 0){
        foreach($subCategories as $subCategory){
            $nbSubProducts = postConstructCategory($subCategory);
            $nbProducts += $nbSubProducts;
        }
    }
    $category->nb_products = $nbProducts;
    
    return $nbProducts;
}

$app->get(
    '/api/shopproducts/{categoryId}',
    function(Request $request, Response $response, $args) {

        global $languageId;
        $languageId = 2;
        
        global $priceListId;
        $priceListId = 1;
        
        $categoryId = $args['categoryId'];
        
        $productsWithoutVariantQuery = TcBern\Model\Product::
        where('category_id', $categoryId)
        ->with(['title','price'])
        ->simpleProduct();
        
        $products = TcBern\Model\Product::
        where('category_id', $categoryId)
        ->with(['title','price', 'variant'])
        ->variantProduct()
        ->unionAll($productsWithoutVariantQuery)
        ->orderBy('code')
        ->get();
        
        $response->getBody()->write($products->toJson());
        
        // global $container;
        // $sql_array = $container['db']->connection()->getQueryLog();
        // $sql_json = json_encode($sql_array);
        // $response->getBody()->write($sql_json);
        return $response;
    }
)->add($headerMw);

$app->get(
    '/api/shopproduct/{productId}',
    function(Request $request, Response $response, $args) {

        global $languageId;
        $languageId = 2;
        
        global $priceListId;
        $priceListId = 1;
        
        $productId = $args['productId'];
        
        $product = TcBern\Model\Product::
        where('id', $productId)
        ->with(['title','price', 'variant'])
        ->variantProduct()
        ->first();
        
        if($product==null){
            $product = TcBern\Model\Product::
                where('id', $productId)
                ->with(['title','price'])
                ->simpleProduct()
                ->first();
        }
        
        if($product!=null){
            return $response->getBody()->write($product->toJson());
        }else{
            return $response->getBody()->write("");
        }
        
        // global $container;
        // $sql_array = $container['db']->connection()->getQueryLog();
        // $sql_json = json_encode($sql_array);
        // $response->getBody()->write($sql_json);
        return $response;
    }
)->add($headerMw);

$app->get(
    '/api/shopvariants/{productId}/{variantId}',
    function(Request $request, Response $response, $args) {
        global $languageId;
        $languageId = 2;
        
        $productId = $args['productId'];
        $variantId = $args['variantId'];
        
        $allProductVariantValues = TcBern\Model\ProductVariantValue::
        where('variant_id', $variantId)
        ->get();
        
        $productVariantValues = array();
        foreach($allProductVariantValues as $allProductVariantValue){
            $productVariantValues[$allProductVariantValue->product_id] []= $allProductVariantValue->variant_value_id;
        }
        
        foreach($productVariantValues as $productId=>$productVariantValue){
            $productVariantValues[$productId] = implode(",",$productVariantValue);
        }
        
        $selectedProductVariantValuesObj = TcBern\Model\ProductVariantValue::
        where('product_id', $productId)
        ->get();
        
        $selectedProductVariantValues = array();
        foreach($selectedProductVariantValuesObj as $selectedProductVariantValueObj){
            $selectedProductVariantValues[$selectedProductVariantValueObj->variant_type_id] = $selectedProductVariantValueObj->variant_value_id;
        }
        
        
        $variant = TcBern\Model\Variant::
        where('id', $variantId)
        ->first();
        
        $temps = array();
        foreach($variant->types as $variantType){
            $variantTypeId = $variantType->id;
            $variantType->selected_value = $selectedProductVariantValues[$variantType->id];
            $variantType->other_values = $variantType->other_values($variantType->selected_value)->pluck('id');
            
            $temps[$variantTypeId] = new stdClass();
            $temps[$variantTypeId]->selected_value = $variantType->selected_value;
            $temps[$variantTypeId]->other_values = $variantType->other_values;
        }
        
        // $otherTemps = array_map(function ($object) { return clone $object; }, $temps); // clone array
        
        // $typeIds = array_keys($temps);
        // $nbTypes = count($typeIds);
        // foreach($temps as $variantTypeId=>$temp){
            

            // for ($i = 0; $i < $nbTypes;$i++ ){
            
            // foreach($otherTemps as $otherVariantTypeId=>$otherTemp){
                
                // $otherSelectedValues = array();
                // if($otherVariantTypeId != $variantTypeId){
                    // $otherSelectedValues []= $otherTemp->selected_value;
                // }
                // $temp->other_selected_values = $otherSelectedValues;
            // }            
        // }
        
        $nbTypes = count($variant->types);
        for ($i = 0; $i < $nbTypes;$i++ ){
            $variantType = $variant->types[$i];
            
            $otherSelectedValues = array();
            foreach($temps as $tempTypeId=>$temp){
                if($variantType->id != $tempTypeId){
                    $otherSelectedValues []= $temp->selected_value;
                }
            }
            $variantType->other_selected_values = $otherSelectedValues;
            
            // foreach($variant->types as $otherVariantType){
                // if($variantType->id != $otherVariantType->id){
                    // $variantType []= $otherVariantType->id;
                // }
            //}
            
            // foreach($variant->values as $variantValue){
                // $productVariantValues = TcBern\Model\ProductVariantValue::
                // where('variant_value_id', $variantValue->id)
                // ->andWhere('variant_value_id', $variantValue->id)
                // ->get();
            // }
        }
        
        $response->getBody()->write(json_encode($variant));
        
        // global $container;
        // $sql_array = $container['db']->connection()->getQueryLog();
        // $sql_json = json_encode($sql_array);
        // $response->getBody()->write($sql_json);
        return $response;
    }
)->add($headerMw);

function postConstructProduct($product){
    if($product != null){
        if($product->product_variant_values){
            $productVariantValueMap = array();
            foreach($product->product_variant_values as $productVariantValue){
                $productVariantValueMap[$productVariantValue->variant_type_id]=$productVariantValue->variant_value_id;
            }
            $product->productVariantValues = $productVariantValueMap;
        }

        $product->makeHidden('product_variant_values');
        
        // if($product->variant){
            // foreach($product->variant->types as $variantType){
                // foreach($product->selectedVariants as $selectedVariant){
                    // if($variantType->id == $selectedVariant->variant_type_id){
                        // $variantType->selectedValueId = $selectedVariant->pivot->variant_value_id;
                    // }
                // }
            // }
        // }
        
    }
}


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
