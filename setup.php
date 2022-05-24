<?php
/*
   ----------------------------------------------------------
   Plugin Avisos 1.0.0
   GLPI 0.85.5 - 9.1.3
  
   Autor: Elena Martínez Ballesta.
   Fecha: Junio 2017

   ----------------------------------------------------------
 */
include (GLPI_ROOT."/plugins/avisos/inc/function.avisos.php");

// Init the hooks of the plugins -Needed
function plugin_init_avisos() {
   global $PLUGIN_HOOKS,$CFG_GLPI, $DB;
   
   // CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm();
   $PLUGIN_HOOKS['csrf_compliant']['avisos'] = true;

   // Configure current profile ...
   $PLUGIN_HOOKS['change_profile']['avisos'] = array('PluginAvisosProfile','changeprofile');
   $PLUGIN_HOOKS['config_page']['avisos'] = 'front/config.php';
     
   $Plugin = new Plugin();
   if ($Plugin->isActivated('avisos')) {
	  // Registro de clases
		//Plugin::registerClass('PluginAvisosAviso'); 	// Avisos
		Plugin::registerClass('PluginAvisosProfile',	// Perfil
			array('addtabon' => array('Profile')));
     	 
		if (Session::haveRight("plugin_avisos",READ)) {
			$PLUGIN_HOOKS['menu_toadd']['avisos'] = array('config' => 'PluginAvisosConfig');
		}
		// Clase PluginAvisosaviso
		$PLUGIN_HOOKS['submenu_entry']['avisos']['options']['aviso'] = array(
			'title' => __('Clases', 'avisos'),
			'page'  =>'/plugins/avisos/front/aviso.php',
			'links' => array(
				'search' => '/plugins/avisos/front/aviso.php',
				'add'    =>'/plugins/avisos/front/aviso.form.php'
		));	
		
//[INICIO] 21/01/18 JDMZ18G ENLAZA EL PLUGIN SIMCARDS CON AVISOS

      if ($Plugin->isActivated('simcard') && class_exists('PluginSimcardSimcard')) {
       array_push($CFG_GLPI['document_types'], 'PluginSimcardSimcard');
      }
		
	//var_dump($CFG_GLPI["document_types"]);	
	//exit();
 
//[FINAL] 21/01/18 JDMZ18G ENLAZA EL PLUGIN SIMCARDS CON AVISOS		
$tipos=$CFG_GLPI["document_types"];

	    if ($Plugin->isActivated('procedimientos') && class_exists('PluginProcedimientosProcedimiento')) {
			if  (!array_search('PluginProcedimientosAccion', $tipos)){ array_push($tipos,"PluginProcedimientosAccion"); }
			if  (!array_search('PluginProcedimientosProcedimiento', $tipos)){ array_push($tipos,"PluginProcedimientosProcedimiento"); }
        }


		if ($Plugin->isActivated('formcreator') && class_exists('PluginFormcreatorForm')) {
			if  (!array_search('PluginFormcreatorForm', $tipos)){ array_push($tipos,"PluginFormcreatorForm"); }
		}

		if ($Plugin->isActivated('indicadores') && class_exists('PluginIndicadoresIndicadore')) {
			if  (!array_search('PluginIndicadoresIndicadore', $tipos)){ array_push($tipos,"PluginIndicadoresIndicadore"); }
		}
				
		foreach ($tipos as $type) { // Los elementos nuevos creados en plugins hay que incluirlos en la constante $CFG_GLPI["document_types"]
													     // Para que llegue el nombre del plugin a este plugin es necesario desactivar y activar plugin avisos.
														 			
			$PLUGIN_HOOKS['item_add']['avisos'][$type] = 'plugin_avisos_check_item';
			
			// Los elementos de Plugins es necesario definirlos aqui.
			if ($type == 'PluginProcedimientosAccion'){ //Acción de un procedimiento
				$formulario = "accion.form.php?";
			} else if ($type == 'PluginProcedimientosProcedimiento'){ // Procedimiento 
				$formulario = "procedimiento.form.php?";
			} else if ($type == 'PluginFormcreatorForm'){ // Formulario
				$formulario = "form.form.php?";
			} else if ($type == 'PluginIndicadoresIndicadore'){ // Indicador
				$formulario = "indicadore.form.php?";				
		    } else if ($type == 'PluginSimcardSimcard'){ // Simcard 21/01/18 JDMZ18G
				$formulario = "simcard.form.php?";				
			} else {
				$formulario = strtolower($type).".form.php?";
			}
			if ((strpos($_SERVER['REQUEST_URI'], $formulario) !== false) && (strpos($_SERVER['REQUEST_URI'], "withtemplate=") === false)) {//exclude template
				$item = getItemForItemtype($type);
				if (isset($_GET['id'])){
					$item->getFromDB($_GET['id']);
					plugin_avisos_check_form_item($item);	
				}
			}			
		}
		if ($Plugin->isActivated('genericobject')) {
			$select_gobject = "SELECT distinct itemtype FROM `glpi_plugin_genericobject_types`;";
			$result_gobject = $DB->query($select_gobject);
			$num_rows = $DB->numrows($result_gobject);
	
			if ($num_rows > 0){
				//while ($row = $DB->fetch_array($result_gobject, MYSQL_NUM)) {
				  while ($row = $DB->fetch_array($result_gobject, MYSQLI_NUM)) { // [CRI] [JMZ18G] MYSQL_NUM deprecated function
				  
					$itemtype = $row['itemtype'];
					$PLUGIN_HOOKS['item_add']['avisos'][$itemtype] = 'plugin_avisos_check_item';
					
					$formulario = "object.form.php?itemtype=".$itemtype."&id=";
					if (strpos($_SERVER['REQUEST_URI'], $formulario) !== false) {					
						//$item = getItemForItemtype($itemtype); //CRI: Modificado por olb26s para los Objetos GenericObject
						$item = new PluginGenericobjectType($itemtype);
						if (isset($_GET['id'])){
							$item->getFromDB($_GET['id']);
							plugin_avisos_check_form_item($item);
						}
					}						
				}
			}
		}
   }	
	return $PLUGIN_HOOKS;
}


// Get the name and the version of the plugin
function plugin_version_avisos() {

   return array('name'          => _n('Avisos' , 'Avisos' ,2, 'Avisos'),
                'version'        => '1.0.1',
                'license'        => 'AGPL3',
                'author'         => '<a href="http://www.carm.es">CARM</a>',
                'homepage'       => 'http://www.carm.es',
                'minGlpiVersion' => '0.85');
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_avisos_check_prerequisites() {

   // GLPI must be at least 0.84 ...
   if (version_compare(GLPI_VERSION,'0.85','lt')) {
      echo "This plugin requires GLPI >= 0.85";
      return false;
   }
   return true;
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_avisos_check_config($verbose=false) {
   if (true) {
      // Always true ...
      return true;
   }

   if ($verbose) {
      _e('Installed / not configured', 'avisos');
   }
   return false;
}
?>
