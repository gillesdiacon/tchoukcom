<?php

class CategoryService extends DbService {

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

}

?>