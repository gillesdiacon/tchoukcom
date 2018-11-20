<?
	$db = mysql_connect('tcbernchzitcbern.mysql.db','tcbernchzitcbern','xuNXsjM8HVxM')
		or die ("Impossible de se connecter à la base de donnée");
		
	$database = "tcbernchzitcbern";

	$selected = mysql_select_db($database, $db) 
		or die ("Impossible de selectionner la base");
?>