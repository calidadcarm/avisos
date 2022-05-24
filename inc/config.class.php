<?php

/*
   ----------------------------------------------------------
   Plugin Avisos 1.0
   GLPI 0.85.5
  
   Autor: Elena Mart�nez Ballesta.
   Fecha: Julio 2016

   ----------------------------------------------------------
 */

if (!defined('GLPI_ROOT')){
   die("Sorry. You can't access directly to this file");
}

class PluginAvisosConfig extends CommonDBTM {
 //  public $table            = 'glpi_plugin_avisos_configs';
 //  public $type             = 'PluginAvisosConfig';
   
   static $rightname = "config";
	
   public static function getTypeName($nb = 0) {
      return __('Avisos', 'Avisos');
   }   
   
   
  static function getMenuContent() {
      global $CFG_GLPI;

      $menu['page'] = "/plugins/avisos/front/config.php";
      $menu['title'] = self::getTypeName();
	  
	  $menu['options']['general']['page']               = "/plugins/avisos/front/general.php";
      $menu['options']['general']['title']              = __("Configuraci&oacute;n general", "Configuraci&oacute;n general");
	  
      $menu['options']['aviso']['page']               = "/plugins/avisos/front/aviso.php";
      $menu['options']['aviso']['title']              = __('Avisos', 'Avisos');
      $menu['options']['aviso']['links']['add']       = '/plugins/avisos/front/aviso.form.php';
      $menu['options']['aviso']['links']['search']    = '/plugins/avisos/front/aviso.php';

	  $menu['options']['right']['page']               = "/plugins/avisos/front/right.form.php";
      $menu['options']['right']['title']              = __("Permisos por aviso", "Permisos por aviso");
      
	  return $menu;
   }


    public function getTabNameForItem(CommonGLPI $item, $withtemplate=0)
   {
      switch ($item->getType()) {
         case "PluginAvisosConfig":
            $object  = new self;
            $found = $object->find();
            $number  = count($found);
            return self::createTabEntry(self::getTypeName($number), $number);
            break;
      }
      return '';
   }     
   
   static function showConfigPage()	 {
       global $CFG_GLPI;
      
		echo "<div class='center'>";
		echo "<table class='tab_cadre'>";
		echo "<tr><th>".__('Configuraci&oacute;n plugin Avisos','Configuraci�n plugin Avisos')."</th></tr>";

		if (Session::haveRight('plugin_avisos', UPDATE)) {
		 
		   // Configuraci�n general
		   echo "<tr class='tab_bg_1 center'><td>";
		   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/avisos/front/general.php' >".__('Configuraci&oacute;n general','Configuraci&oacute;n general')."</a>";
		   echo "</td/></tr>\n";
		   
		 // Gesti�n de avisos
		   echo "<tr class='tab_bg_1 center'><td>";
		   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/avisos/front/aviso.php' >".__('Ver o modificar avisos','Ver o modificar avisos')."</a>";
		   echo "</td/></tr>\n";

		   // Gesti�n de derechos por aviso
		   echo "<tr class='tab_bg_1 center'><td>";
		   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/avisos/front/right.form.php' >".__('Gesti&oacute;n de derechos por aviso','Gesti&oacute;n de derechos por aviso')."</a>";
		   echo "</td/></tr>\n";			   
		}

		echo "</table></div>";
   }
   
 /* public function showForm ($ID, $options=array()) {
	global $CFG_GLPI, $DB;
	
	  // In percent
      $colsize1 = '13';
      $colsize2 = '37';
	  
	 // $this->initForm($ID, $options);
    //  $this->showFormHeader($options);
      $rand   = mt_rand();
      echo "<form name='config_form$rand' id='config_form$rand' method='post'
               action='".Toolbox::getItemTypeFormURL("PluginAvisosConfig")."'>";
      echo "<div class='spaced' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe' id='mainformtable'>";

      echo "<tr class='headerRow'><th colspan='2'>Configuraci&oacute;n de la cabecera</th></tr>";	  
	 // Nombre del Aviso
      echo "<tr class='tab_bg_1'>";
			echo "<th class='left'  colspan='1'>".__('Texto cabecera de los avisos','Texto cabecera de los avisos')."</th>";
			echo "<td class='left'  colspan='3'>";
				Html::autocompletionTextField($this,"cabecera",array('size' => "124"));
			echo "</td>";
      echo "</tr>";

	 // Color de la cabecera
      echo "<tr class='tab_bg_1'>";
			echo "<th class='left'  colspan='1'>".__('Color de la cabecera','Color de la cabecera')."</th>";
			echo "<td class='left'  colspan='3'>";
				Html::autocompletionTextField($this,"color",array('size' => "50"));
			echo "</td>";
      echo "</tr>";
	  
	 // Tama�o de la cabecera
      echo "<tr class='tab_bg_1'>";
			echo "<th class='left'  colspan='1'>".__('Tama&ntilde;o de letra','Tama&ntilde;o de letra')."</th>";
			echo "<td class='left'  colspan='3'>";
			$options = array('name' => "size", 'size' => 10, 'option' => "onkeypress='return event.charCode >= 48 && event.charCode <= 57'" );
			Html::autocompletionTextField($this, "size", $options);				
			echo "</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1' colspan='2' class='center'><td>";	
	  echo "<input type='hidden' name='id' value=".$this->fields['id']."/>";	  
	  echo "<input type='submit' name='update' value=\""._sx('button', 'Update')."\" class='submit'>";
      echo "</td></tr></table>";	  
	  Html::closeForm();
      echo "</div>";
	  echo "</form>";
	  
      return true;
   }*/	  
   
}

?>