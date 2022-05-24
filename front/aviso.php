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

Html::header(__('avisos', 'avisos'), $_SERVER['PHP_SELF'] ,"config", "PluginAvisosConfig", "aviso");

// Check if plugin is activated...
$plugin = new Plugin();
if(!$plugin->isInstalled('avisos') || !$plugin->isActivated('avisos')) {
   Html::displayNotFoundError();
}

if (Session::haveRight('plugin_avisos',UPDATE)) {
	Search::Show('PluginAvisosAviso');
	Html::footer();
} else {
	Html::displayRightError();
}

?>