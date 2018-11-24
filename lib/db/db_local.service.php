<?php

class DbService {
    
    public $mysqli;

    public function __construct(){
        $this->mysqli = new mysqli("localhost", "tchoukcom", "mxCh7WzsI3yOb0TF");
        $this->mysqli->select_db("tchoukcom");
        $this->mysqli->set_charset("utf8");
    }

    function fillObjectWithSQLResult($className,$result){
        $array = array();
        
        while($object=$result->fetch_object($className)){
            $array []= $object;
        }
        
        return $array;
    }

}

?>