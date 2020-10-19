<?php

class ProductService extends DbService {
    
    public $priceListId = 1;
    
    function __construct() {
        parent::__construct();
    }

    function getAllProductsByCategoryId($categoryId, $langId){
        $simpleProducts = $this->getProductsByCategoryId($categoryId, $langId, "simple");
        $variantProducts = $this->getProductsByCategoryId($categoryId, $langId, "variant");

        return array_merge($simpleProducts, $variantProducts);
    }
    
    function getProductsByCategoryId($categoryId, $langId, $productType){
        $query  = "select id, category_id, code, variant_id, title, small_description, description, price from product ";
        $query .= "join product_i18n on (product_i18n.product_id=product.id) ";
        $query .= "join price on (price.product_id=product.id and price_list_id=$this->priceListId) ";
        $query .= "where(";
        $query .= "  product_i18n.language_id=$langId ";
        $query .= "  and category_id=$categoryId";
        if($productType == "simple"){
            $query .= "  and variant_id=0";
        }else if($productType == "variant"){
            $query .= "  and variant_id!=0";
        }
        $query .= ") ";
        if($productType == "variant"){
            $query .= "group by variant_id ";
        }
        $query .= "order by id";
        
        $result = $this->mysqli->query($query) or die("error with table product");
        $products = parent::fillObjectWithSQLResult("Product",$result);
        
        if($productType == "variant"){
            foreach($products as $product){
                if ($product->variant_id) {
                    $product->variant_types = $this->getVariantTypes($product->id, $product->variant_id, $langId);
                }
            }
        }

        return $products;
    }

    function getProductById($productId, $langId){
        $query  = "select id, category_id, code, variant_id, title, small_description, description, price from product ";
        $query .= "join product_i18n on (product_i18n.product_id=product.id) ";
        $query .= "join price on (price.product_id=product.id and price_list_id=$this->priceListId) ";
        $query .= "where(";
        $query .= "  product_i18n.language_id=$langId ";
        $query .= "  and id=$productId";
        $query .= ") ";
        
        $result = $this->mysqli->query($query) or die("error with table product");

        $products = parent::fillObjectWithSQLResult("Product",$result);
        $product = $products[0];
        
        if(isset($product->variant_id)){
            $product->variant_types = $this->getVariantTypes($productId, $product->variant_id, $langId);
        }

        return $product;
    }

    function getVariantTypes($productId, $variantId, $langId){
        $query  = "select id, name from variant_type ";
        $query .= "join variant_type_i18n on (variant_type_i18n.variant_type_id=variant_type.id) ";
        $query .= "where(variant_type_i18n.language_id=$langId and variant_id=$variantId) ";
        $query .= "order by id";
        
        $result = $this->mysqli->query($query) or die("error with table variant_type");
        $variantTypes = parent::fillObjectWithSQLResult("VariantType",$result);
        
        $previousTypePossibleProductIds = array();
        foreach($variantTypes as $variantType){
            $selectedValueId = $this->getVariantValueId($productId, $variantType->id);
            $variantType->selectedValueId = $selectedValueId;

            $variantType->values = $this->getVariantValues($variantType->id, $langId, $previousTypePossibleProductIds);
            $previousTypePossibleProductIds = $this->getPossibleProductId($variantId, $variantType->id, $selectedValueId);
        }
        
        return $variantTypes;
    }

    function getVariantValues($variantTypeId, $langId, $previousTypePossibleProductIds){
        $query  = "select id, name from variant_value ";
        $query .= "join variant_value_i18n on (variant_value_i18n.variant_value_id=variant_value.id) ";
        $query .= "where(variant_value_i18n.language_id=$langId and variant_type_id=$variantTypeId) ";
        $query .= "order by id";
        
        $result = $this->mysqli->query($query) or die("error with table variant_value");
        $variantValues = parent::fillObjectWithSQLResult("VariantValue",$result);
        
        foreach($variantValues as $variantValue){
            $variantValue->product_id = $this->getVariantProductId($variantTypeId, $variantValue->id, $previousTypePossibleProductIds);
        }
        
        return $variantValues;
    }

    function getVariantProductId($variantTypeId, $variantValueId, $previousTypePossibleProductIds){
        $query  = "select product_id from product_variant_value ";
        $query .= "join product on product.id = product_variant_value.product_id ";
        $query .= "where(variant_type_id=$variantTypeId and variant_value_id=$variantValueId ";
        
        if(!empty($previousTypePossibleProductIds)){
            $query .= " and product_id in (" . implode(",", $previousTypePossibleProductIds) . ")";
        }
        
        $query .= ") ";
        
        $result = $this->mysqli->query($query) or die("error with table product_variant_value\nquery:" . $query);
        $row = $result->fetch_assoc();
        
        return $row["product_id"];
    }
    
    function getVariantValueId($productId, $variantTypeId){
        $query  = "select variant_value_id from product_variant_value ";
        $query .= "where(product_id=$productId and variant_type_id=$variantTypeId) ";
        
        $result = $this->mysqli->query($query) or die("error with table product_variant_value\nquery:" . $query);
        $row = $result->fetch_assoc();
        
        return $row["variant_value_id"];
    }

    function getPossibleProductId($variantId, $variantTypeId, $variantValueId){
        $query  = "select product_id from product_variant_value ";
        $query .= "where(variant_id=$variantId and variant_type_id=$variantTypeId and variant_value_id=$variantValueId) ";
        
        $possibleProductId = array();
        if ($variantValueId != null) {
            $result = $this->mysqli->query($query) or die("error with table product_variant_value\nquery:" . $query);
            while ($row = $result->fetch_assoc()) {
                $possibleProductId []= $row["product_id"];
            }
        }
        
        return $possibleProductId;
    }

}

?>