<?php
    require_once("init.php");
    
    $dbService = new DbService();
    $categoryService = new CategoryService($dbService);
    $productService = new ProductService($dbService);
    
    $langId = getLangId($lang);
    
    $rootCategoryId = 10;
    $selectedCategoryId = getGETvalOrDefault("categoryId", $rootCategoryId);
    $selectedProductId = getGETval("productId");
    
    // get root categories
    $categories = $categoryService->getCategoriesByParentId($rootCategoryId, $langId);

    // get selected category
    $selectedCategory = $categoryService->getCategoryById($selectedCategoryId, $langId);
    $selectedCategory->sub_categories = $categoryService->getCategoriesByParentIdWithNbProd($selectedCategoryId, $langId);
    
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
        $products = $productService->getAllProductsByCategoryId($selectedCategory->id, $langId);
        
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
<html lang="<?php echo $lang ?>">
    <head>
        <?php 
            $headTitle = "Shop";
            require_once("head.php"); 
        ?>
    </head>
    <body>
        <div class="container mx-auto">

            <?php require_once("banner.php"); ?>
            
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
    
            <?php require_once("footer.php"); ?>
            
        </div>
    
        <?php require_once("finish.php"); ?>

    </body>
</html>
