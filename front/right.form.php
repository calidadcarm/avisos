<?php

/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */
 
include_once ("../../../inc/includes.php");

Session::checkRight('profile', READ);

Plugin::load('avisos', true);

Html::header(__('avisos', 'avisos'), $_SERVER['PHP_SELF'] ,"config", "PluginAvisosConfig", "right");

require_once "../inc/profile.class.php";

$aviso = new PluginAvisosAviso();
if (isset($_POST['plugin_avisos_avisos_id'])) {
	$aviso -> getFromDB($_POST['plugin_avisos_avisos_id']);
}

$warning =''; // Variable que contiene el id del aviso
if (isset($_POST['aviso'])) {
   $warning=$_POST['aviso'];
}
$prof = new PluginAvisosProfile();

if (isset($_POST['delete']) && $warning) {
   $profile_right = new ProfileRight;
   $profile_right->deleteByCriteria(array('name' => "plugin_avisos_aviso_$aviso"));
   ProfileRight::addProfileRights(array("plugin_avisos_aviso_$aviso"));

} else if (isset($_POST['update']) && $warning) {
   Session::checkRight('profile', UPDATE);   
   if (PluginAvisosProfile::updateForAviso($_POST)){
		$_POST['plugin_avisos_avisos_id'] = $warning; 
		$aviso -> getFromDB($_POST['plugin_avisos_avisos_id']);	
   }
}
echo "<br>";
echo "<form method='post' action=\"".$_SERVER["PHP_SELF"]."\">";
echo "<table class='tab_cadre_fixe' align='center'><tr><th colspan='2'>";
echo __('Selecciona un aviso para ver o editar sus permisos de acceso por perfiles', 'avisos'). "</th></tr>\n";

echo "<tr class='tab_bg_1'><td align='center'>Aviso&nbsp;&nbsp;&nbsp; ";
PluginAvisosAviso::dropdown(array('name' => 'plugin_avisos_avisos_id'));

echo "&nbsp;&nbsp;<input type='submit' value='"._sx('button', 'Post')."' class='submit' ></td></tr>";
echo "</table><br>";
Html::closeForm();

if (isset($_POST['plugin_avisos_avisos_id'])) {
   PluginAvisosProfile::showForAviso($aviso);
}

Html::footer();
