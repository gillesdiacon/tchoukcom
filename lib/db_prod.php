<?
	$db = mysql_connect('tcbernchzitcbern.mysql.db','tcbernchzitcbern','xuNXsjM8HVxM')
		or die ("Impossible de se connecter � la base de donn�e");
		
	$database = "tcbernchzitcbern";

	$selected = mysql_select_db($database, $db) 
		or die ("Impossible de selectionner la base");
?>