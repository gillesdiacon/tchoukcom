<?php

class DbService {

    public $mysqli;

    public function __construct(){
        $this->mysqli = new mysqli("tcbernchzitcbern.mysql.db", "tcbernchzitcbern", "xuNXsjM8HVxM");
        $this->mysqli->select_db("tcbernchzitcbern");
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