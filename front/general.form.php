<?php

/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */

include ("../../../inc/includes.php");
                       

global $DB;

if (isset($_POST["update"])) {
	
	$query = "UPDATE `glpi_plugin_avisos_configs` SET cabecera='".$_POST['cabecera']."', color='".$_POST['color']."', size='".$_POST['size']."'
			  WHERE id=1;";
	$result = $DB->query($query);
	
	Html::redirect($_SERVER['HTTP_REFERER']);
}                                              

?>