<?php

    function getMysqli(){
        $mysqli = new mysqli("localhost", "tchoukcom", "mxCh7WzsI3yOb0TF");
        $mysqli->select_db("tchoukcom");
        $mysqli->set_charset("utf8");
        return $mysqli;
    }
?>