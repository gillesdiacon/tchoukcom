<?
	$db = mysql_connect('localhost','tchoukcom','mxCh7WzsI3yOb0TF')
		or die ("Impossible de se connecter � la base de donn�e");
		
	$database = "tchoukcom";

	$selected = mysql_select_db($database, $db) 
		or die ("Impossible de selectionner la base");
?>