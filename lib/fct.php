<?php

function changeLangParam($url,$lang){
	$hasLang = false;
	$paramStr = "";
	$urlArray = explode("?",$url);
	$script = $urlArray[0];
	
	if(count($urlArray)>1){
		$paramArray = explode ("&",$urlArray[1]);
		
		foreach($paramArray as $param){
			if(substr($param,0,5) == "lang="){
				$paramStr .= "&lang=" . $lang;
				$hasLang = true;
			}else{
				$paramStr .= "&" . $param;
			}
		}
	}
	if(!$hasLang){
		$paramStr .= "&lang=" . $lang;
	}
	return $script . "?" . substr($paramStr,1);
}

function getPOSTval($field,$default){
	$val = $default;
	if(isset($_POST[$field])){
		$val=$_POST[$field];
	}
	return $val;
}

function getGETval($field,$default){
	$val = $default;
	if(isset($_GET[$field])){
		$val=$_GET[$field];
	}
	return $val;
}

?>