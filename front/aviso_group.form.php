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


$PluginAvisosAviso_Group = new PluginAvisosAviso_Group();

 if (isset($_POST["addgroup"])) {   
   if ($_POST['groups_id']>0) {
       $PluginAvisosAviso_Group ->addItem($_POST);
   }
   Html::back();  
} else if (isset($_POST["elimina"])){
	$query= "delete from glpi_plugin_avisos_avisos_groups where plugin_avisos_avisos_id=".$_POST["plugin_avisos_avisos_id"]."
			 and groups_id=".$_POST["elimina"];
    $DB->query($query);
	Html::back();

} else {
	  
   Html::header(__('Avisos', 'avisos'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginAvisosConfig",
      "aviso"
 );
			   
   $PluginAvisosAviso_Group->display($_GET["id"]);
   Html::footer();
   
}
?>
