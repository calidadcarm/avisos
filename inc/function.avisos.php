<?php
/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */

/*function makeSELECT($select, $items_id){
	  $select = str_replace("IDITEM",$items_id, $select);
	
	  return $select;
}*/

function formatSQL($request) {

      $request = str_replace("&lt;","<",$request);
      $request = str_replace("&gt;", ">", $request);

      return $request;
}	

// Devuelve true si el elemento necesita ser avisado
// porque la select no devuelve campo vacío.
function necesita_aviso($select_item, $items_id, $table){
	global $DB;
	
	$select = $select_item. " and `".$table."`.`id`=".$items_id;
	$select_item = formatSQL($select);
	//echo $select;
	$result_select_item = $DB->query($select_item);
	$num_rows_select_item = $DB->numrows($result_select_item);
			
	if ($num_rows_select_item > 0){
		return true;
	} else{
		return false;
	}
}
 
 
// Devuelve true si un grupo tiene acceso a un aviso
// y false si no lo tiene
function mostrar_aviso_grupo($avisos_id, $groups_id){
	global $DB;
	
	$select = "SELECT * FROM glpi_plugin_avisos_avisos_groups
			   WHERE plugin_avisos_avisos_id=".$avisos_id." and groups_id='".$groups_id."';";
			   
	$result = $DB->query($select);
	$num_rows = $DB->numrows($result);
	if ($num_rows>0){
		return true;
	} else{
		return false;
	}
}
 
// Función que obtiene los IDs de los avisos que aplican a un item
// Sólo aquellos definidos para ese elemento y que estén activos
function get_avisos_from_item($item){
	global $DB;
	
	$itemtype = $item->getType();
	if (isset($item->field['entities_id'])) {
		$entidad = "and entities_id=$item->field['entities_id'];";
	} else {
		$entidad = ";";
	}
	//$entity = $item->getEntityID();
	$select_avisos = "SELECT `id`, `query`, `name`, `itemtype`, `color`, `make`, `show` FROM glpi_plugin_avisos_avisos
					  WHERE itemtype='".$itemtype."' and active=1 and is_deleted=0 ".$entidad;
	$result = $DB->query($select_avisos);
	$num_rows = $DB->numrows($result);
	$avisos = NULL;
	if ($num_rows > 0){
		$users_id = Session::getLoginUserID(); // Usuario
		$groups = Group_User::getUserGroups($users_id); // Grupos a los que pertenece el usuario
		
	  //while ($row = $DB->fetch_array($result, MYSQL_NUM)) {
		while ($row = $DB->fetch_array($result, MYSQLI_NUM)) {	// [CRI] [JMZ18G] MYSQL_NUM deprecated function
			$id = $row['id'];
			$right = "plugin_avisos_aviso_".$id;
			if (Session::haveRight($right, CREATE)){
				// Si aplica chequear y mostrar el aviso (revisando perfil)
				$select = $row['query'];
				$text = $row['name'];
				$itemtype = $row['itemtype'];
				$color = $row['color'];
				$make =  $row['make'];
				$show = $row['show'];
				$avisos[] = array('id' => $id,
                              'query'    => $select,
                              'name'    => $text,
							  'itemtype'=> $itemtype,
							  'color'    => $color,
							  'make' => $make,
							  'show' => $show);
			} else { // Comprobar si tengo acceso a ese aviso por el grupo al que pertenezco				
				$mostrar = false; 
				foreach ($groups as $group => $grupo){
					if (mostrar_aviso_grupo($id, $grupo['id']) == true) {
						// Si aplica chequear y mostrar el aviso (revisando grupo)
						$select = $row['query'];
						$text = $row['name'];
						$itemtype = $row['itemtype'];
						$color = $row['color'];
						$make =  $row['make'];
						$show = $row['show'];						
						$avisos[] = array('id' => $id,
                              'query'    => $select,
                              'name'    => $text,
							  'itemtype'=> $itemtype,
							  'color'    => $color,
							  'make' => $make,
							  'show' =>$show);
					}
				}
			}
		}
	}
    return $avisos;		
}

// Función que llama al mostrar el elemento
function plugin_avisos_check_form_item($item){
	global $CFG_GLPI, $DB;
	
	$text = "";
	if ((isset($item->fields['id'])) && ($item->fields['id']>0)){
		$id = $item->fields['id'];
		$item->getFromDB($id);
		$table = $item->getTable();
		$avisos = get_avisos_from_item($item);
		if ($avisos!= NULL){
			foreach ($avisos as $aviso) {
				if ($aviso['show'] == 1){
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
			if ($text !== ""){
				$query = "Select * from `glpi_plugin_avisos_configs` where id='1';";
				$result = $DB->query($query);
				$config = $DB->fetch_array($result);

		$tabla='<table border="0">
			  <tr>
				<td align="right"><img style="vertical-align:middle;" alt="" src="'.$_SESSION["glpiroot"].'/plugins/avisos/imagenes/system-attention-icon.png"></td>
				<td class="left">
				<font color="'.$config['color'].'" SIZE="'.$config['size'].'"> '.$config['cabecera'].'</font>	
				</td>
			  </tr>		
			  <tr>				
				<td colspan="2" class="center">
				--------------------------------------------------------------------<br>
				<strong>'.$text.'</strong>
				--------------------------------------------------------------------<br>
				</td>
			  </tr>				  
		</table>';
		
		Session::addMessageAfterRedirect(__($tabla, 'genericobject'),true, INFO, false);						
				
			/*	Session::addMessageAfterRedirect("<img src='".$CFG_GLPI["root_doc"]."/plugins/avisos/imagenes/warning.png' alt='Avisos' align='absmiddle'><font color='".$config['color']."' SIZE='".$config['size']."'> ".$config['cabecera']."<br></font>".$text,true, INFO, false);*/
			} else {
			// $_SESSION["MESSAGE_AFTER_REDIRECT"] = '';
			}
		}
	}
}

?>