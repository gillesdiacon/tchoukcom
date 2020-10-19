<?php

class CategoryService extends DbService {
    
    var $productService;
    
    function __construct($productServiceParam) {
        parent::__construct();
        
        $this->productService = $productServiceParam;
    }

    function getCategoriesByParentId($categoryParentId, $langId){
        $query  = "select id, parent_id, name, description, image_filename from category ";
        $query .= "join category_i18n on (category_i18n.category_id=category.id) ";
        //$query .= "join category_rank on (category_rank.category_id=category.id) ";
        $query .= "where(category_i18n.language_id=$langId and parent_id=$categoryParentId) ";
        //$query .= "order by category_rank.rank";
        $query .= "order by id";
        
        $result = $this->mysqli->query($query) or die("error with table category");
        $categories = parent::fillObjectWithSQLResult("Category",$result);
        
        return $categories;
    }
    
    function getCategoriesByParentIdWithNbProd($categoryParentId, $langId){
        $categories = $this->getCategoriesByParentId($categoryParentId, $langId);
        
        foreach($categories as $category){
            $category->nb_products = $this->recursiveGetNbProducts($category->id, $langId);
        }
        
        return $categories;
    }

    function getCategoryById($categoryId, $langId){
        $query  = "select id, parent_id, name, description, image_filename from category ";
        $query .= "join category_i18n on (category_i18n.category_id=category.id) ";
        //$query .= "join category_rank on (category_rank.category_id=category.id) ";
        $query .= "where(category_i18n.language_id=$langId and id=$categoryId) ";
        //$query .= "order by category_rank.rank";
        $query .= "order by id";

        $result = $this->mysqli->query($query) or die("error with table category");
        $categories = parent::fillObjectWithSQLResult("Category",$result);

        return $categories[0];
    }

    function recursiveGetNbProducts($categoryId, $langId){
        $subCategories = $this->getCategoriesByParentId($categoryId, $langId);

        $nbProducts = 0;
        if(count($subCategories) > 0){
            // there are some sub categories
            // sum nb product for each sub category
            foreach($subCategories as $subCategory){
                $nbProducts += $this->recursiveGetNbProducts($subCategory->id, $langId);
            }
        }else{
            $nbProducts = count($this->productService->getAllProductsByCategoryId($categoryId, $langId));
        }

        return $nbProducts;
    }

}

?>