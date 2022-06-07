<?php

/*
   ----------------------------------------------------------
   Plugin Procedimientos 2.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2016

   ----------------------------------------------------------
 */

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");

// Check if plugin is activated...
$plugin = new Plugin();

if ($plugin->isActivated("avisos")) {
	
	   Html::header("Configuraci&oacuten; general",
      $_SERVER['PHP_SELF'],
      "config",
      "PluginAvisosConfig",
      "avisos"
   );
/*  
if (!isset($_SESSION['glpi_js_toload']['colorpicker'])) {
            echo Html::css('lib/jqueryplugins/spectrum-colorpicker/spectrum.css');
            Html::requireJs('colorpicker');
}
*/
	
	$haveright = Session::haveRight("plugin_avisos",UPDATE);

	//////////// Show the form /////////////
	if ($haveright) {
	  
	$data = new PluginAvisosConfig();
	$data->getFromDB(1);	
//var_dump($data);	  
	  
      $rand   = mt_rand();
		
	  echo '<div class="center">';
	  //echo '<a href="./config.php">Volver a Configuración de Avisos</a><br><br>';
	  echo "<br><br>";
      echo "<form name='config_form$rand' id='config_form$rand' method='post'
               action='".$CFG_GLPI["root_doc"]."/plugins/avisos/front/general.form.php'>";
      echo "<table class='tab_cadre_fixe' id='mainformtable'>";

      echo "<tr class='headerRow'><th colspan='2'>Configuraci&oacute;n de la cabecera</th></tr>";	   
	 // Nombre del Aviso
      echo "<tr class='tab_bg_1'>";
			echo "<th style='background-color: #f9fbfb;' width='300px'>".__('Texto cabecera de los avisos','Texto cabecera de los avisos')."</th>";
			echo "<td class='left'>";
			echo "<input name='cabecera' size='120' value='".$data->fields['cabecera']."'/>";
			echo "</td>";
      echo "</tr>";

	 // Color de la cabecera
      echo "<tr class='tab_bg_1'>";
			echo "<th style='background-color: #f9fbfb;' class='left' width='150px'>".__('Color de la cabecera','Color de la cabecera')."</th>";
			echo "<td class='left' >";

	  	  if (empty($data->fields['color'])) { $color="#000"; } else { $color=$data->fields['color']; }
	
	$rand = mt_rand();

         echo "<div class='fa-label'>
            <i class='fas fa-tint fa-fw' title='".__('Color')."'></i>";
         $rand = mt_rand();
		//echo "<input name='color' size='20' value='".$data->fields['color']."'/>";
		Html::showColorField('color', ['value' => $color, 'rand' => $rand]);		
         echo "</div>";	

			echo "</td>";
      echo "</tr>";
	  
	 // Tamaño de la cabecera
      echo "<tr class='tab_bg_1'>";
			echo "<th style='background-color: #f9fbfb;' class='left' width='150px'>".__('Tamaño de letra','Tamaño de letra')."</th>";
			echo "<td class='left'>";
				
         echo "<div class='fa-label'>
            <i class='fas fa-sort-amount-up fa-fw' title='".__('Color')."'></i>";
         $rand = mt_rand();
		//echo "<input name='color' size='20' value='".$data->fields['color']."'/>";
		Dropdown::showNumber("size", ['value' => $data->fields['size']]);		
         echo "</div>";	
				
			
			
				//echo "<input name='size' size='20' value='".$data->fields['size']."'/>";		
			echo "</td>";
      echo "</tr>";
	  
	  // Guardar
      echo "<tr class='tab_bg_1'><td class='center' colspan='2'>";	
	  echo "<input type='submit' name='update' value=\""._sx('button', 'Update')."\" class='submit'>";
      echo "</td></tr></table><br><br>";	  
	  Html::closeForm();		
   } else  {
		Html::displayRightError();	   
   }
   Html::footer();

} else {
   Html::displayNotFoundError();
}  
?>