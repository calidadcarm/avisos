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
                       

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";

$PluginAvisosAviso = new PluginAvisosAviso();

if (isset($_POST["add"])) {	
	$newID=$PluginAvisosAviso->add($_POST);
    Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["delete"])) {

	$PluginAvisosAviso->delete($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginAvisosAviso'));
	
} else if (isset($_POST["restore"])) {

	$PluginAvisosAviso->check($_POST['id'],'w');
	$PluginAvisosAviso->restore($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginAvisosAviso'));
	
} else if (isset($_POST["purge"])) {

	$PluginAvisosAviso->delete($_POST,1);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginAvisosAviso'));
	
} else if (isset($_POST["update"])) {
	
	$PluginAvisosAviso->update($_POST);
	Html::redirect($_SERVER['HTTP_REFERER']);
 }                                          
  else {
	  
   Html::header(__('Avisos', 'avisos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginAvisosConfig",
      "aviso"
   );
/*
if (!isset($_SESSION['glpi_js_toload']['colorpicker'])) {
            echo Html::css('lib/jqueryplugins/spectrum-colorpicker/spectrum.css');
            Html::requireJs('colorpicker');
}    
*/
    if (Session::haveRight('plugin_avisos',UPDATE)) {		
		$PluginAvisosAviso ->display(array('id' => $_GET["id"]));
		Html::footer();
	} else {
			Html::displayRightError();
	}     
}
?>