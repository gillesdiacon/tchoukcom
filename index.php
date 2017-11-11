<?php
    require_once("lib/fct.php");
    
    $lang = getGETval("lang","fr");
    $selectedCategoryId = getGETval("categoryId", 10);
    
    // get category tree
    $rootCategory;
    $selectedCategory;
    $parentCategory;
    $URL = "http://localhost/tchoukcom/backend/v1/public/api/shopcategory/10";
    $dataStr = file_get_contents($URL);
    if (!empty($dataStr)) {
        $rootCategory = json_decode($dataStr);
        
        if($selectedCategoryId == $rootCategory->id){
            $selectedCategory = $rootCategory;
        }

        foreach($rootCategory->sub_categories as $category){
            if($selectedCategoryId == $category->id){
                $selectedCategory = $category;
                $parentCategory = $category;
            }else if($category->sub_categories){
                foreach($category->sub_categories as $subCategory){
                    if($selectedCategoryId == $subCategory->id){
                        $selectedCategory = $subCategory;
                        $parentCategory = $category;
                    }
                }
            }
        }
    }
    
    // get products
    $products;
    if(isset($selectedCategory) && !$selectedCategory->sub_categories){
        $URL = "http://localhost/tchoukcom/backend/v1/public/api/shopproducts/".$selectedCategoryId;
        $dataStr = file_get_contents($URL);
        if (!empty($dataStr)) {
            $products = json_decode($dataStr);
    }
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" type="image/png" href="tp.png" />
    
        <title>Shop | Tchoukball Promotion</title>
    
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <link href="css/tp.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mx-auto">
    
            <div id="header" class="masthead mb-3 row row-eq-height no-gutters">
                <div class="col-1"></div>
                <div id="bannerLogo" class="text-left col-md-auto pt-3">
                    <a href="http://tchouk.com/index.php" title="Tchoukball Promotion">
                        <img src="images/logo_<?php echo $lang; ?>.png" alt="Tchoukball Promotion">
                    </a>
                </div>
                <div id="globalMenu" class="col d-flex align-items-end pl-5">
                    <ul>
                        <li class="active last">
                            <a class="row row-eq-height no-gutters" href="index.php<?php if($lang!="fr"){echo"?lang=".$lang;}?>">
                                <div class="col-md-auto">
                                    <img src="images/pageTitle_left_act.png" />
                                </div>
                                <div class="col-md-auto pageTitleCenterAct font-italic text-white d-flex align-items-end pr-2">
                                    <strong>Shop</strong>
                                </div>
                                <div class="col-md-auto">
                                    <img src="images/pageTitle_right_act.png" />
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="bannerText" class="text-right col-md-auto">
                    <img src="images/bannerText_<?php echo $lang; ?>.png" alt="Faisons tchouker le monde!" title="Faisons tchouker le monde!">
                </div>
                <div id="languageMenu" class="col-md-auto pr-3">
                    <ul>
                        <li class="<?php if($lang=="fr"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "fr") ?>" title="Afficher cette page en français" xml:lang="fr" lang="fr">FR</a>
                        </li>
                        <li class="<?php if($lang=="de"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "de") ?>" title="Show this page in German" xml:lang="de" lang="de">DE</a>
                        </li>
                        <li class="<?php if($lang=="en"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "en") ?>" title="Show this page in English" xml:lang="en" lang="en">EN</a>
                        </li>
                        <li class="<?php if($lang=="it"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "it") ?>" title="Show this page in Italian" xml:lang="it" lang="it">IT</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="row">
                <div class="col-2 categoryCol">
                    <div id="categoryMenu" class="box">
                        <table id="title" class="ml-5">
                            <tr>
                                <td>
                                    <img src="images/titleLeft_big.png" />
                                </td>
                                <td class="titleCenter font-italic text-white">
                                    <strong>Shop</strong>
                                </td>
                                <td>
                                    <img src="images/titleRight_big.png" />
                                </td>
                            </tr>
                        </table>
                        <ul class="pl-0">
                            <?php
                                foreach($rootCategory->sub_categories as $category){
                                    $categoryClass = "";
                                    if($selectedCategoryId == $category->id || $parentCategory->id == $category->id){
                                        $categoryClass = "selected";
                                    }
                                    echo "<li class='pl-2 " . $categoryClass . "'>";
                                        echo "<a href='".changeParam("categoryId", $category->id)."'>".$category->title->name."</a>";
                                        if($category->sub_categories && ($selectedCategoryId == $category->id || $parentCategory->id == $category->id)){
                                            foreach($category->sub_categories as $subCategory){
                                                $subCategoryClass = "";
                                                if($selectedCategoryId == $subCategory->id){
                                                    $subCategoryClass = "selected";
                                                }
                                                echo "<li class='pl-4 " . $subCategoryClass . "'>";
                                                    echo "<a href='".changeParam("categoryId", $subCategory->id)."'>".$subCategory->title->name."</a>";
                                                echo "</li>";
                                            }
                                        }
                                    echo "</li>";
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col">
                    <div id="shopContainer" class="shopBox box">
                        <?php 
                            if(isset($selectedCategory) && $selectedCategory->sub_categories){
                                echo "<div class='row row-nested row-eq-height'>";
                                    foreach($selectedCategory->sub_categories as $selectedSubCategory){
                                        echo "<div class='col-lg-3 categoryItemCol'>";
                                            echo "<div class='categoryItem'>";
                                                echo "<a href='".changeParam("categoryId", $selectedSubCategory->id)."'>";
                                                    $categoryImage = "categoryImages/".$selectedSubCategory->title->image_filename;
                                                    $imgSrc = file_exists($categoryImage)?$categoryImage:"categoryImages/noCategoryImage.jpg";
                                                    echo "<img class='img-fluid' src='".$imgSrc."'"
                                                        ."alt='".$selectedSubCategory->title->name."'" 
                                                        ."title='".$selectedSubCategory->title->name."'"
                                                        ."/>";
                                                    echo "<div class='categoryName'>".$selectedSubCategory->title->name."</div>";
                                                    echo "<div class='numberOfProd'>(".$selectedSubCategory->nb_products." Produits)</div>";
                                                echo "</a>";
                                            echo "</div>";
                                        echo "</div>";
                                    }
                                echo "</div>";
                            } else if(isset($selectedCategory) && isset($products) && count($products)>0){
                                echo "<div class='row row-nested row-eq-height'>";
                                    foreach($products as $product){
                                        echo "<div class='col-lg-3 productItemCol'>";
                                            echo "<div class='productItem'>";
                                                echo "<a href='".changeParam("productId", $product->id)."'>";
                                                    $productImage = "productImages/".$product->code."_photoppale.jpg";
                                                    $imgSrc = file_exists($productImage)?$productImage:"productImages/noProductImage.jpg";
                                                    echo "<img class='img-fluid mx-auto d-block' src='".$imgSrc."'"
                                                        ."alt='".$product->title->title."'" 
                                                        ."title='".$product->title->title."'"
                                                        ."/>";
                                                    echo "<div class='productTitle'>".$product->title->title."</div>";
                                                    echo "<div class='productCode'>Réf: ".$product->code."</div>";
                                                    echo "<div class='productDescription'>".mb_strimwidth($product->title->small_description,0,50," ...")."</div>";
                                                echo "</a>";
                                                
                                                if($product->variant && $product->variant->types){
                                                    foreach($product->variant->types as $variantType){
                                                        echo "<div class='productVariant'>";
                                                            echo "<div class='variantTitle'>".$variantType->name->name.":</div>";
                                                            echo "<ul class='pl-2'>";
                                                                foreach($variantType->values as $variantValue){
                                                                    $variantValueClass = "";
                                                                    if($variantValue->id == $variantType->selectedValueId){
                                                                        $variantValueClass = "active";
                                                                    }
                                                                    echo "<li class='px-1 m-1 ".$variantValueClass."'"
                                                                        ."title='".$variantType->name->name.": ".$variantValue->name->name."'>";
                                                                        echo "<a href=''>".$variantValue->name->name."</a>";
                                                                    echo "</li>";
                                                                }
                                                            echo "</ul>";
                                                        echo "</div>";
                                                    }
                                                }
                                                echo "<div class='mt-5'>";
                                                    echo "<div class='text-right m-3 productPrice'>".$product->price->price." chf</div>";
                                                echo "</div>";

                                            echo "</div>";
                                        echo "</div>";
                                    }
                                echo "</div>";
                            } else {
                                echo "<div class='text-center'>Pas de produit pour cette catégorie</div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="col-2">
                
                </div>
            </div>
            
            <!--
                <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-3">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav text-md-center nav-justified w-100">
                            <li class="nav-item">
                                <a class="nav-link" href="index.html">Accueil <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contacts.html">Contacts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="gallery.html">Gallerie Photos</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="reservations.php">Réservations</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            -->
            
            <footer id="footer" class="px-3 mt-5">
                <p class="text-muted p-1">&copy;&nbsp;Tchoukball&nbsp;Promotion
                    &nbsp;-&nbsp;
                    <a href="conditions.html">Conditions g&eacute;n&eacute;rales de vente</a>
                    &nbsp;-&nbsp;
                    <a href="contact.html">Contact</a></p>
            </footer>
    
        </div>
    
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
        <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
        <!-- <script src="../../../../assets/js/vendor/holder.min.js"></script> -->
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
