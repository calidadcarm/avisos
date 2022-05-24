<?php
/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */


// Install process for plugin : need to return true if succeeded
function plugin_avisos_install() {
   global $DB;

   Toolbox::logInFile("avisos", "Plugin installation\n");
   
   include_once (GLPI_ROOT."/plugins/avisos/inc/profile.class.php");
  
   if (!$DB->TableExists("glpi_plugin_avisos_avisos")){
		$DB->runFile(GLPI_ROOT . '/plugins/avisos/sql/install.sql');
   } 
      
   PluginAvisosProfile::initProfile();
   PluginAvisosProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);	
   
   return true;
}


// Uninstall process for plugin : need to return true if succeeded
function plugin_avisos_uninstall() {
   global $DB;
   
   $notif = new Notification();
   $options = array('itemtype' => 'Ticket',
                    'event'    => 'plugin_avisos',
                    'FIELDS'   => 'id');
   foreach ($DB->request('glpi_notifications', $options) as $data) {
      $notif->delete($data);
   }   
   
   return true;
}


function plugin_avisos_postinit() {
   global $CFG_GLPI;
   
   return true;
}

// Función que llama al crear un elemento.
function plugin_avisos_check_item($item){
	global $CFG_GLPI, $DB;
	$text = "";
	$id = $item->fields['id'];
	$avisos = get_avisos_from_item($item);
	$table = $item->getTable();
	
	if ($avisos!= NULL){
		foreach ($avisos as $aviso) {
			if ($aviso['make'] == 1){
				if (necesita_aviso($aviso['query'], $id, $table)){
					if (!isset($aviso['color'])){
						$color = '#000080';
					} else {
						$color = $aviso['color'];
					}
					$text = "<font color='".$color."'>".$aviso['name']."</font><br>".$text;
				}
			}				
		}
		if ($text!=''){
				  
			$query = "Select * from `glpi_plugin_avisos_configs` where id='1';";
			$result = $DB->query($query);
			$config = $DB->fetch_array($result);
	  
			Session::addMessageAfterRedirect("<img src='".$CFG_GLPI["root_doc"]."/plugins/avisos/imagenes/warning.png' alt='Avisos' align='absmiddle'><font color='".$config['color']."' SIZE='".$config['size']."'> ".$config['cabecera']."<br></font>".$text,false, INFO, false);
		}
	}
}

function plugin_avisos_getAddSearchOptions($itemtype) {
    global $LANG, $CFG_GLPI;

    $sopt = array();

   if (Session::haveRight('plugin_avisos', READ)) {

	 if ($itemtype == 'PluginAvisosAviso') {
		 
		// Grupos que pueden visualizar un aviso.
		$sopt[101]['table']     = 'glpi_groups';
		$sopt[101]['field']     = 'name';	 
		$sopt[101]['name']      = 'Grupos con visibilidad';
		$sopt[101]['datatype']      = 'itemlink';
		$sopt[101]['linkfield'] = 'groups_id';
		$sopt[101]['forcegroupby'] = true;
		$sopt[101]['splititems']   = false;		
		$sopt[101]['massiveaction'] = false;				  
		$sopt[101]['joinparams'] = array('beforejoin'
										=> array('table'      => 'glpi_plugin_avisos_avisos_groups',
												'joinparams' => array('jointype' => 'child')));
												
	 }
													 
   }
  return $sopt;
}

?>