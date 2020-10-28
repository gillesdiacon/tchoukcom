<?php

class DbService {
    
    public $mysqli;

    public function __construct(){
        $this->mysqli = new mysqli("localhost", "ktt_tchoukshop", "mxCh7WzsI3yOb0Tk");
        $this->mysqli->select_db("ktt_tchoukshop");
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