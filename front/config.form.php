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
                       

$PluginAvisosConfig = new PluginAvisosConfig();

if (isset($_POST["add"])) {	
	$newID=$PluginAvisosConfig->add($_POST);
    Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["update"])) {
	
	$PluginAvisosConfig->update($_POST);
	Html::redirect($_SERVER['HTTP_REFERER']);
 }                                          
  else {
	  
   Html::header(__('Configuraci&oacute;n general', 'Configuraci&oacute;n general'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginAvisosConfig",
      "config"
   );
    if (Session::haveRight('plugin_avisos',UPDATE)) {
		$PluginAvisosConfig ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}     
}
?>