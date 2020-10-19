<?php

class ProductCountService extends DbService {
    
    function __construct() {
        parent::__construct();
    }

    function countAllProductsByCategoryId($categoryId, $langId){
        $nbSimpleProducts = $this->countProductsByCategoryId($categoryId, "simple");
        $nbVariantProducts = $this->countProductsByCategoryId($categoryId, "variant");

        return $nbSimpleProducts + $nbVariantProducts;
    }
    
    function countProductsByCategoryId($categoryId, $productType){
        $query  = "select id from product ";
        $query .= "where(";
        $query .= "  category_id=$categoryId";
        if($productType == "simple"){
            $query .= "  and variant_id=0";
        }else if($productType == "variant"){
            $query .= "  and variant_id!=0";
        }
        $query .= ") ";
        if($productType == "variant"){
            $query .= "group by variant_id ";
        }
        
        $result = $this->mysqli->query($query) or die("error with count on table product");
        
        return $result->num_rows;
    }
}

?>