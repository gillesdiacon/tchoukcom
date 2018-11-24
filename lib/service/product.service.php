<?php

class ProductService extends DbService {
    
    public $priceListId = 1;

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
                    $product->variant_types = $this->getVariantTypes($product->variant_id, $langId);
                }
            }
        }

        return $products;
    }

    function getVariantTypes($variantId, $langId){
        $query  = "select id, name from variant_type ";
        $query .= "join variant_type_i18n on (variant_type_i18n.variant_type_id=variant_type.id) ";
        $query .= "where(variant_type_i18n.language_id=$langId and variant_id=$variantId) ";
        $query .= "order by id";
        
        $result = $this->mysqli->query($query) or die("error with table variant_type");
        $variantTypes = parent::fillObjectWithSQLResult("VariantType",$result);
        
        foreach($variantTypes as $variantType){
            $variantType->values = $this->getVariantValues($variantType->id, $langId);
        }
        
        return $variantTypes;
    }

    function getVariantValues($variantTypeId, $langId){
        $query  = "select id, name from variant_value ";
        $query .= "join variant_value_i18n on (variant_value_i18n.variant_value_id=variant_value.id) ";
        $query .= "where(variant_value_i18n.language_id=$langId and variant_type_id=$variantTypeId) ";
        $query .= "order by id";
        
        $result = $this->mysqli->query($query) or die("error with table variant_value");
        return parent::fillObjectWithSQLResult("VariantValue",$result);
    }

}

?>