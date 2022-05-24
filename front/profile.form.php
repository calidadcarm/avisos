<?php
/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */
 
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");
include_once (GLPI_ROOT."/plugins/avisos/inc/profile.class.php");

Session::checkRight("profile","w");
$prof=new PluginAvisosProfile();


//Save profile
if (isset ($_POST['UPDATE'])) {
	$prof->update($_POST);
	Html::back();
}

?>
