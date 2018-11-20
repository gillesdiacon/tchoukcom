<?
	$db = mysql_connect('localhost','tchoukcom','mxCh7WzsI3yOb0TF')
		or die ("Impossible de se connecter à la base de donnée");
		
	$database = "tchoukcom";

	$selected = mysql_select_db($database, $db) 
		or die ("Impossible de selectionner la base");
?>