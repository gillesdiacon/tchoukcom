<?php
    require_once("lib/load.php");
    $dbService = new DbService();
    $categoryService = new CategoryService($dbService);
    $productService = new ProductService($dbService);
    
    $lang = getGETvalOrDefault("lang","fr");
    $langId = getLangId($lang);
    
    $rootCategoryId = 10;
    $selectedCategoryId = getGETvalOrDefault("categoryId", $rootCategoryId);
    $selectedProductId = getGETval("productId");
    
    // get root categories
    $categories = $categoryService->getCategoriesByParentId($rootCategoryId, $langId);

    // get selected category
    $selectedCategory = $categoryService->getCategoryById($selectedCategoryId, $langId);
    $selectedCategory->sub_categories = $categoryService->getCategoriesByParentId($selectedCategoryId, $langId);
    
    // get sub categories
    $subCategories = array();
    if ($selectedCategoryId != $rootCategoryId) {
        foreach($categories as $category){
            if ($selectedCategoryId == $category->id || $selectedCategory->parent_id == $category->id) {
                $category->sub_categories = $categoryService->getCategoriesByParentId($category->id, $langId);
            }
        }
    }

    // get products
    $products;
    if(isset($selectedCategory) && !$selectedCategory->sub_categories){
        $simpleProducts = $productService->getProductsByCategoryId($selectedCategory->id, $langId, "simple");
        $variantProducts = $productService->getProductsByCategoryId($selectedCategory->id, $langId, "variant");

        $products = array_merge($simpleProducts, $variantProducts);
        
        // oder by id
        usort(
            $products, 
            function($a, $b){
                return strcmp($a->id, $b->id);
            }
        );
    }
    
    // get product
    $selectedProduct;
    if(isset($selectedCategory) && isset($selectedProductId)){
        $selectedProduct = $productService->getProductById($selectedProductId, $langId);
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
                <div class="col-2 p-0 categoryCol">
                    <div id="categoryMenu" class="box">
                        <table class="menuTitle_big ml-5">
                            <tr>
                                <td class="p-0">
                                    <img src="images/titleLeft_big.png" />
                                </td>
                                <td class="titleCenter font-italic text-white p-0">
                                    <strong>Shop</strong>
                                </td>
                                <td class="p-0">
                                    <img src="images/titleRight_big.png" />
                                </td>
                            </tr>
                        </table>
                        <ul class="pl-0">
                            <?php                            
                                foreach($categories as $category){
                                    $categoryClass = "";
                                    if($selectedCategoryId == $category->id || $selectedCategory->parent_id == $category->id){
                                        $categoryClass = "selected";
                                    }
                                    echo "<li class='pl-2 " . $categoryClass . "'>";
                                        echo "<a href='?categoryId=".$category->id."'>".$category->name."</a>";
                                        foreach($category->sub_categories as $subCategory){
                                            $subCategoryClass = "";
                                            if($selectedCategoryId == $subCategory->id){
                                                $subCategoryClass = "selected";
                                            }
                                            echo "<li class='pl-4 " . $subCategoryClass . "'>";
                                                echo "<a href='?categoryId=".$subCategory->id."'>".$subCategory->name."</a>";
                                            echo "</li>";
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
                            if(!empty($selectedCategory->sub_categories)){
                                echo "<div class='row row-nested row-eq-height'>";
                                    foreach($selectedCategory->sub_categories as $selectedSubCategory){
                                        echo "<div class='col-lg-3 categoryItemCol'>";
                                            echo "<div class='categoryItem'>";
                                                echo "<a href='".changeParam("categoryId", $selectedSubCategory->id)."'>";
                                                    $categoryImage = "categoryImages/".$selectedSubCategory->image_filename;
                                                    $imgSrc = file_exists($categoryImage)?$categoryImage:"categoryImages/noCategoryImage.jpg";
                                                    echo "<img class='img-fluid' src='".$imgSrc."'"
                                                        ."alt='".$selectedSubCategory->name."'" 
                                                        ."title='".$selectedSubCategory->name."'"
                                                        ."/>";
                                                    echo "<div class='categoryName'>".$selectedSubCategory->name."</div>";
                                                    echo "<div class='numberOfProd'>(".$selectedSubCategory->nb_products." Produits)</div>";
                                                echo "</a>";
                                            echo "</div>";
                                        echo "</div>";
                                    }
                                echo "</div>";
                            } else if(isset($selectedCategory) && isset($selectedProduct)){
                                echo "<div class='p-3'>";
                                    echo "<div class='px-3 productItem productDetail'>";
                                        echo "<div class='pl-4 productTitle'>".$selectedProduct->title."</div>";
                                        echo "<div class='pl-3 productCode'>Réf: ".$selectedProduct->code."</div>";
                                        echo "<div class='productDescription'>".$selectedProduct->small_description."</div>";
                                        
                                        echo "<div class='row row-nested row-eq-height'>";
                                            echo "<div class='col-lg-4'>";
                                                $productImage = "productImages/".$selectedProduct->code."_photoppale.jpg";
                                                $imgSrc = file_exists($productImage)?$productImage:"productImages/noProductImage.jpg";
                                                echo "<img class='img-fluid mx-auto d-block' src='".$imgSrc."'"
                                                    ."alt='".$selectedProduct->title."'" 
                                                    ."title='".$selectedProduct->title."'"
                                                    ."/>";
                                            echo "</div>";
                                            echo "<div class='col-lg productPanel'>";
                                                if(!empty($selectedProduct->variant_types)){
                                                    foreach($selectedProduct->variant_types as $variantType){
                                                        echo "<div class='productVariant'>";
                                                            echo "<div class='variantTitle'>".$variantType->name.":</div>";
                                                            echo "<ul class='pl-2'>";
                                                                foreach($variantType->values as $variantValue){
                                                                    $variantValueClass = "";
                                                                    $disabledLinkClass = "";
                                                                    $targetProductId;
                                                                    if($variantValue->product_id == null){
                                                                        $disabledLinkClass = "disabledLink";
                                                                        $targetProductId = null;
                                                                    } else if($variantType->selectedValueId == $variantValue->id){
                                                                        $variantValueClass = "active";
                                                                        $targetProductId = $selectedProduct->id;
                                                                    } else {
                                                                        $targetProductId = $variantValue->product_id;
                                                                    }
                                                                    echo "<li class='px-1 m-1 ".$variantValueClass." ".$disabledLinkClass."'"."title='".$variantType->name.": ".$variantValue->name."'>";
                                                                        if ($targetProductId == null) {
                                                                            echo $variantValue->name;
                                                                        } else {
                                                                            echo "<a href='".changeParam("productId", $targetProductId)."'>".$variantValue->name."</a>";
                                                                        }
                                                                        //echo "<a href=''>".$variantValue->name."</a>";
                                                                    echo "</li>";
                                                                }
                                                            echo "</ul>";
                                                        echo "</div>";
                                                    }
                                                }
                                                echo "<div class='mt-5'>";
                                                    echo "<div class='text-right m-3 productPrice'>".$selectedProduct->price." chf</div>";
                                                echo "</div>";
                                            echo "</div>";
                                        echo "</div>";

                                    echo "</div>";
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
                                                        ."alt='".$product->title."'" 
                                                        ."title='".$product->title."'"
                                                        ."/>";
                                                    echo "<div class='productTitle'>".$product->title."</div>";
                                                    echo "<div class='productCode'>Réf: ".$product->code."</div>";
                                                    echo "<div class='productDescription'>".mb_strimwidth($product->small_description,0,50," ...")."</div>";
                                                echo "</a>";
                                                
                                                if(!empty($product->variant_types)){
                                                    foreach($product->variant_types as $variantType){
                                                        echo "<div class='productVariant'>";
                                                            echo "<div class='variantTitle'>".$variantType->name.":</div>";
                                                            echo "<ul class='pl-2'>";
                                                                foreach($variantType->values as $variantValue){
                                                                    $variantValueClass = "";
                                                                    $disabledLinkClass = "";
                                                                    $targetProductId;
                                                                    if ($variantValue->product_id == null) {
                                                                        $disabledLinkClass = "disabledLink";
                                                                        $targetProductId = null;
                                                                    } else if($variantType->selectedValueId == $variantValue->id) {
                                                                        $variantValueClass = "active";
                                                                        $targetProductId = $product->id;
                                                                    } else {
                                                                        $targetProductId = $variantValue->product_id;
                                                                    }
                                                                    echo "<li class='px-1 m-1 ".$variantValueClass."'"."title='".$variantType->name.": ".$variantValue->name."'>";
                                                                        if($targetProductId == null){
                                                                            echo $variantValue->name;
                                                                        } else {
                                                                            echo "<a href='".changeParam("productId", $targetProductId)."' class='".$disabledLinkClass."'>".$variantValue->name."</a>";
                                                                            //echo "<a href=''>".$variantValue->name."</a>";
                                                                        }
                                                                    echo "</li>";
                                                                }
                                                            echo "</ul>";
                                                        echo "</div>";
                                                    }
                                                }
                                                echo "<div class='mt-5'>";
                                                    echo "<div class='text-right m-3 productPrice'>".$product->price." chf</div>";
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
                <div class="col-2 p-0">
                    <div class="box">
                        <table class="menuTitle" class="ml-2">
                            <tr>
                                <td class="p-0">
                                    <img src="images/titleLeft.png" />
                                </td>
                                <td class="titleCenter font-italic text-white p-0">
                                    <strong>Contacts</strong>
                                </td>
                                <td class="p-0">
                                    <img src="images/titleRight.png" />
                                </td>
                            </tr>
                        </table>

                        <div class="addressBox font-weight-bold p-2">
                            <div class="addressPhone">
                                <span class="addressLabel">T:&nbsp;</span>+41 (0) 22 368 00 41
                            </div>
                            <div class="addressFax">
                                <span class="addressLabel">F:&nbsp;</span>+41 (0) 22 368 00 28
                            </div>
                            <div class="addressEmail">
                                <span class="addressLabel">@:&nbsp;</span>info@tchouk.com
                            </div>
                        </div>

                        <div class="addressBox p-2">
                            <div class="addressLabel">Tchoukball Promotion</div>
                            <div>3, avenue Edmond Vaucher</div>
                            <div>1219 Châtelaine, Suisse</div>
                        </div>

                        <div class="addressBox p-2">
                            <div class="addressLabel">Tchoukball Promotion Europe</div>
                            <div>606, chemin des Hautins</div>
                            <div>01280 Prévessin Moens, France</div>
                        </div>
                    </div>
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
            
            <?php
                $agb_fr = "Conditions g&eacute;n&eacute;rales de vente";
                $agb_de = "Allgemeine Gesch&auml;ftsbedingungen";
                $agb_en = "General terms and conditions";
                $agb_it = "Condizioni generali di vendita";
                
                $contact_fr = "Contact";
                $contact_de = "Kontakt";
                $contact_en = "Contact";
                $contact_it = "Contatto";
            ?>
            
            <footer id="footer" class="px-3 mt-5">
                <p class="text-muted p-1">&copy;&nbsp;Tchoukball&nbsp;Promotion
                    &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="conditions.html<?php if($lang!="fr"){echo"?lang=".$lang;}?>"><?php echo ${'agb_' . $lang} ?></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="contact.html<?php if($lang!="fr"){echo"?lang=".$lang;}?>"><?php echo ${'contact_' . $lang} ?></a></p>
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
