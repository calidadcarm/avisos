<?php
/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Martínez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
        die("Sorry. You can't access directly to this file");
}

// Class of the defined type
class PluginAvisosAviso extends CommonDBTM {
   
   public $dohistory=true;

   const CONFIG_PARENT   = - 2;
   
   // From CommonDBTM
   public $table            = 'glpi_plugin_avisos_avisos';
   public $type             = 'PluginAvisosAviso';

   static $rightname = "plugin_avisos";
   
   public static function getTypeName($nb=0) {
      return __('Avisos', 'Avisos');
   }
 

   /**
    * Get search function for the class
    *
    * @return array of search option
   **/
   
	 function rawSearchOptions() {

	$tab = [];

	$tab = array_merge($tab, parent::rawSearchOptions());


	$tab[] = [
	'id' => '100',
	'table' => $this->getTable(),
	'field' => 'name',
	'name' => __('Aviso','Aviso'),
	'datatype' => 'itemlink',
	'massiveaction' => false,
	];


	$tab[] = [
	'id' => '101',
	'table' => $this->getTable(),
	'field' => 'comment',
	'name' => __('Descripcion','Descripcion'),
	'datatype' => 'text',
	'massiveaction' => true,
	];	

	$tab[] = [
	'id' => '102',
	'table' => $this->getTable(),
	'field' => 'query',
	'name' => __('Query','Query'),
	'datatype' => 'text',
	'massiveaction' => false,
	];	
	
	$tab[] = [
	'id' => '103',
	'table' => $this->getTable(),
	'field' => 'color',
	'name' => __('Color','Color'),
	'datatype' => 'text',
	'massiveaction' => false,
	];	
	
	$tab[] = [
	'id' => '104',
	'table' => $this->getTable(),
	'field' => 'make',
	'name' => __('Avisar al crear','Avisar al crear'),
	'datatype' => 'bool',
	'massiveaction' => true,
	];	
	
	$tab[] = [
	'id' => '105',
	'table' => $this->getTable(),
	'field' => 'show',
	'name' => __('Avisar al mostrar','Avisar al mostrar'),
	'datatype' => 'bool',
	'massiveaction' => true,
	];
	
	$tab[] = [
	'id' => '106',
	'table' => $this->getTable(),
	'field' => 'active',
	'name' => __('Activo','Activo'),
	'datatype' => 'bool',
	'massiveaction' => true,
	];	
	
	$tab[] = [
	'id' => '107',
	'table' => $this->getTable(),
	'field' => 'date_mod',
	'name' => __('Fecha de modificaci&oacute;n','Fecha de modificaci&oacute;n'),
	'datatype' => 'datetime',
	'massiveaction' => false,
	];	
	
	$tab[] = [
	'id' => '108',
	'table' => 'glpi_entities',
	'field' => 'completename',
	'name' => _n('Entity', 'Entities', 1),
	'datatype' => 'dropdown',
	'massiveaction' => true,
	];		
	
	return $tab;

	} 


   
      //define header form
  function defineTabs($options=array()) {

      $ong = array();
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('PluginAvisosAviso', $ong, $options);
	  $this->addStandardTab('PluginAvisosAviso_Group', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);
      return $ong;
   }
   
  public function showForm ($ID, $options=array()) {
	global $CFG_GLPI, $DB;
	
	  // In percent
      $colsize1 = '13';
      $colsize2 = '37';
	  
	  $this->initForm($ID, $options);
      $this->showFormHeader($options);
	  
	 //Nombre del Aviso
      echo "<tr class='tab_bg_1'>";
			echo "<th class='left'  colspan='1'>".__('Texto aviso','Texto aviso')."</th>";
			echo "<td class='left'  colspan='3'>";
				Html::autocompletionTextField($this,"name",array('size' => "124"));
			echo "</td>";
      echo "</tr>";

	  // Descripción del aviso
	  echo "<tr class='tab_bg_1'>";
	  echo "<th class='left'  colspan='1'>Descripción</th>";
	  echo "<td class='left' colspan='3'><textarea cols='125' rows='3' name='comment'>".
            $this->fields["comment"]."</textarea>";
      echo "</td></tr>";
	  
	  // Select del elemento
	  echo "<tr class='tab_bg_1'>";
	  echo "<th class='left' width='$colsize1%' colspan='1'>Query</th>";
      echo "<td class='left' colspan='3'><textarea cols='125' rows='10' name='query'>".
            $this->fields["query"]."</textarea>";
      echo "</td></tr>";
	  
	  echo "<tr class='tab_bg_1'>";
	  echo "<th class='left' width='$colsize1%' colspan='1'>Tipo de elemento</th>";
      echo "<td class='left' colspan='1'>";
	  
	           echo "<div class='fa-label'>
            <i class='fas fa-filter fa-fw'
               title='".__('Tipo de elemento')."'></i>";
			   
Dropdown::showItemTypes('itemtype', $CFG_GLPI["document_types"], array('value' => $this->fields['itemtype'], 'title' => __('Tipo de elemento')));
			   
            echo "</div>"; 
	  
	  
	  echo "</td></tr>";
	   
	  // Selecciona color hexadecimal
	  echo "<tr class='tab_bg_1'>";
      echo "<th class='left'  colspan='1'>Color<br>(hexadecimal)</th>";
	  echo "<td class='left'  widht='30px'>";
	  
	  	  if (empty($this->fields['color'])) { $color="#000"; } else { $color=$this->fields['color']; }
	
	$rand = mt_rand();

         echo "<div class='fa-label'>
            <i class='fas fa-tint fa-fw' title='".__('Color')."'></i>";
         $rand = mt_rand();
		
		Html::showColorField('color', ['value' => $color, 'rand' => $rand]);
         echo "</div>";	
        
        
      
			//Html::autocompletionTextField($this,"color",array('size' => "10"));
	  echo "</td>";

	   
	// En qué evento se muestra el aviso.
	  echo "<tr class='tab_bg_1'>";
	  echo "<th class='left'  colspan='1'>Avisar al crear</th>";
	  echo "<td>";
         echo "<div class='fa-label'>
           <i class='fas fa-plus fa-fw' title='".__('Avisar al crear')."'></i> &nbsp;";         
		   
         echo "<span class='switch pager_controls'>
            <label for='makeswitch$rand' title='".__('Avisar al crear')."'>
               <input type='hidden' name='make' value='0'>
                              <input type='checkbox' id='makeswitch$rand' name='make' value='1'".
                     ($this->fields["make"]
                        ? "checked='checked'"
                        : "")."
               >
               <span class='lever'></span>
            </label>
         </span>
		 </div>";			   		  	
	  echo "</td></tr>";	  

	// En qué evento se muestra el aviso.
	  echo "<tr class='tab_bg_1'>";
	  echo "<th class='left'  colspan='1'>Avisar al mostrar</th>";
	  echo "<td>";
		  	 
         echo "<div class='fa-label'>
           <i class='fas fa-eye fa-fw' title='".__('Avisar al mostrar')."'></i> &nbsp;";  

         echo "<span class='switch pager_controls'>
            <label for='showswitch$rand' title='".__('Avisar al mostrar')."'>
               <input type='hidden' name='show' value='0'>
                              <input type='checkbox' id='showswitch$rand' name='show' value='1'".
                     ($this->fields["show"]
                        ? "checked='checked'"
                        : "")."
               >
               <span class='lever'></span>
            </label>
         </span>
		 </div>";		   		
         	
	  echo "</td></tr>";		  
	   
	  echo "<tr class='tab_bg_1'>";
	  echo "<th class='left'  colspan='1'>Activo</th>";
	  echo "<td class='left'  widht='10px'>";
	  
         echo "<div class='fa-label'>
            <i class='fas fa-lock fa-fw' title='".__('Activo')."'></i> &nbsp;";
         $rand = mt_rand();
         echo "<span class='switch pager_controls'>
            <label for='activeswitch$rand' title='".__('Activo')."'>
               <input type='hidden' name='active' value='0'>
                              <input type='checkbox' id='activeswitch$rand' name='active' value='1'".
                     ($this->fields["active"]
                        ? "checked='checked'"
                        : "")."
               >
               <span class='lever'></span>
            </label>
         </span>";
         echo "</div>";		  
	  
	  
			//Dropdown::showYesNo("active",$this->fields["active"]);
      echo "</td>";
	  echo "</tr>";	  
	  
	// Ultima modificación
	echo "<tr>";
	  echo "<td class='center' colspan='4'>";
      printf(__('Last update on %s'), Html::convDateTime($this->fields["date_mod"]));
      echo "</td>";
	echo "</tr>";
	  $this->showFormButtons($options);
	    
      return true;
   }

  static function DropdownItem($myname, $value=0){
	global $DB,$CFG_GLPI;
	$query = "select id, name from glpi_plugin_avisos_avisos where order by 1";
		$result=$DB->query($query);
		//Desplegable avisos
		echo "<select name=$myname id=$myname>\n";
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				echo "<option value='".$data[0]."'>".$data[1]."</option>\n";			
			}
		}
		echo "</select>\n";		
		
		
	}   

  static function formatSQL($request) {

      $request = str_replace("&lt;","<",$request);
      $request = str_replace("&gt;", ">", $request);

      return $request;
  }	

  static function makeSELECT($select,$items_id){
	  $select = str_replace("IDITEMTYPE",$items_id, $select);
	  return $select;
  }
 
 	  	
}
?>