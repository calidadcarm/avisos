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

class PluginAvisosProfile extends Profile {

   static $rightname = "profile";

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if ($item->getType()=='Profile') {
            return PluginAvisosAviso::getTypeName(2);
      }
      return '';
   }


   static function DisplayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      if ($item->getType()=='Profile') {
         $ID = $item->getID();
         $prof = new self();

         self::addDefaultProfileInfos($ID, 
                                    array('plugin_avisos' => 0));
         $prof->showForm($ID);
      }
      return true;
   }
   
   static function getIcon() {
		return "fas fa-user-lock";
	 }

   static function createFirstAccess($ID) {
      //85
      self::addDefaultProfileInfos($ID,
                                    array('plugin_avisos' => 127), true);
   }
   
    /**
    * @param $profile
   **/
  static function addDefaultProfileInfos($profiles_id, $rights, $drop_existing = false) {
      global $DB;
      
      $profileRight = new ProfileRight();
      foreach ($rights as $right => $value) {
		  
		  $criteria = [
"profiles_id" => $profiles_id,
"name" => $right,
];
		  
         if (countElementsInTable('glpi_profilerights',
                                   $criteria) && $drop_existing) {
            $profileRight->deleteByCriteria(array('profiles_id' => $profiles_id, 'name' => $right));
         }
         if (!countElementsInTable('glpi_profilerights',
                                   $criteria)) {
            $myright['profiles_id'] = $profiles_id;
            $myright['name']        = $right;
            $myright['rights']      = $value;
            $profileRight->add($myright);

            //Add right to the current session
            $_SESSION['glpiactiveprofile'][$right] = $value;
         }
      }
   }


   /**
    * Show profile form
    *
    * @param $items_id integer id of the profile
    * @param $target value url of target
    *
    * @return nothing
    **/
   function showForm($profiles_id=0, $openform=TRUE, $closeform=TRUE) {

      echo "<div class='firstbloc'>";
      if (($canedit = Session::haveRightsOr(self::$rightname, array(CREATE, UPDATE, PURGE)))
          && $openform) {
         $profile = new Profile();
         echo "<form method='post' action='".$profile->getFormURL()."'>";
      }

      $profile = new Profile();
      $profile->getFromDB($profiles_id);
      if ($profile->getField('interface') == 'central') {
         $rights = $this->getAllRights();	 
         $profile->displayRightsChoiceMatrix($rights, array('canedit'       => $canedit,
                                                         'default_class' => 'tab_bg_2',
                                                         'title'         => __('General')));
		
		 $rights = $this->getExtraRights();	 
		 $profile->displayRightsChoiceMatrix($rights, array('default_class' => 'tab_bg_2',
                                                    'title'         => __('Permisos adicionales')));

   	  }
       
      if ($canedit
          && $closeform) {
         echo "<div class='center'>";
         echo Html::hidden('id', array('value' => $profiles_id));
         echo Html::submit(_sx('button', 'Save'), array('name' => 'update'));
         echo "</div>\n";
         Html::closeForm();
      }
      echo "</div>";
   }

   static function getAllRights($all = false) {
      $rights = array(
          array('itemtype'  => 'PluginAvisosAviso',
                'label'     => _n('Avisos', 'Avisos', 2, 'avisos'),
                'field'     => 'plugin_avisos'
          ),
      );
      
      return $rights;
   }
   
 static function getExtraRights($all = false) {
	 global $DB;
	 
	$rights = array();
	
	$query = "Select id, name from glpi_plugin_avisos_avisos";
	$result = $DB->query($query);	

   /* 
   
   // [INICIO] [CRI] [JMZ18G] fetch_array deprecated function

	$num_rows = $DB->numrows($result);	
	if ($num_rows > 0){
      //while ($row = $DB->fetch_array($result, MYSQL_NUM)) {
        while ($row = $DB->fetch_array($result, MYSQLI_NUM)) {	// [CRI] [JMZ18G] MYSQL_NUM deprecated function			
			    $field = 'plugin_avisos_aviso_'.$row['id'];
				$rights[] = array('rights'    => array(CREATE  => __('Permitir')),
                           'label'    => "Acceso al aviso '".$row['name']."'",
                           'field'    => $field); 
		}
	}

   */

  if ($result && $DB->numrows($result)) {
      while ($data = $DB->fetchAssoc($result)) {
         $field = 'plugin_avisos_aviso_'.$data['id'];
         $rights[] = array('rights'    => array(CREATE  => __('Permitir')),
                        'label'    => "Acceso al aviso '".$data['name']."'",
                        'field'    => $field); 
      }
   }

  // [FINAL] [CRI] [JMZ18G] fetch_array deprecated function

      return $rights;
 }  
   
   
   /**
    * Init profiles
    *
    **/
    
   static function translateARight($old_right) {
      switch ($old_right) {
         case '': 
            return 0;
         case 'r' :
            return READ;
         case 'w':
            return ALLSTANDARDRIGHT + READNOTE + UPDATENOTE;
         case '0':
         case '1':
            return $old_right;
            
         default :
            return 0;
      }
   }
      
   /**
   * Initialize profiles, and migrate it necessary
   */
   static function initProfile() {
      global $DB;
      $profile = new self();

      //Add new rights in glpi_profilerights table
      foreach ($profile->getAllRights(true) as $data) {
		  
		  $criteria = [
"name" => $data['field'],
];
		  
         if (countElementsInTable("glpi_profilerights", 
                                  $criteria) == 0) {
            ProfileRight::addProfileRights(array($data['field']));
         }
      }
      foreach ($DB->request("SELECT *
                           FROM `glpi_profilerights` 
                           WHERE `profiles_id`='".$_SESSION['glpiactiveprofile']['id']."' 
                              AND `name` LIKE '%plugin_avisos%'") as $prof) {
         $_SESSION['glpiactiveprofile'][$prof['name']] = $prof['rights']; 
      }
   }

   
  static function removeRightsFromSession() {
      foreach (self::getAllRights(true) as $right) {
         if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
            unset($_SESSION['glpiactiveprofile'][$right['field']]);
         }
      }
   }

   /**
    * @param $report
   **/
   static function showForAviso(PluginAvisosAviso $aviso) {
      global $DB;
	  
	  $avisos_id = $aviso->fields['id'];
      if (empty($aviso) || !Session::haveRight('profile', READ)) {
         return false;
      }
	  
      $canedit = Session::haveRight('profile', UPDATE);

      if ($canedit) {
         echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
      }

      echo "<table class='tab_cadre' width='300px'>\n";
      echo "<tr><th colspan='2'>'".$aviso->fields['name']."'</th></tr>";
	  echo "<tr class='tab_bg_1'><td width='60%'  bgcolor='#F6E3CE' align='center'>PERFIL</td><td width='40%'  bgcolor='#F6E3CE' align='center'>ACCESO</td></tr>";
      $query = "SELECT `id`, `name`
                FROM `glpi_profiles`
                ORDER BY `name`";

      foreach ($DB->request($query) as $data) {
		  
		  $permisos = "select rights from glpi_profilerights
					   where name='plugin_avisos_aviso_".$avisos_id ."' 
					   and profiles_id=".$data['id'].";";
		  
		  $result = $DB->query($permisos);
		//$rights = $DB->fetch_array($result, MYSQL_NUM);
		//$rights = $DB->fetch_array($result, MYSQLI_NUM); // [CRI] [JMZ18G] MYSQL_NUM deprecated function
        $rights = $DB->fetchAssoc($result); //[CRI] [JMZ18G] fetch_array deprecated function
          echo "<tr class='tab_bg_1'><th style='background-color: #f9fbfb;' width='70%' align='left'>" . $data['name'] . "&nbsp: </th><td align='center' width='40%'>";

$rand = mt_rand();
        
         echo "<span class='switch pager_controls'>
            <label for='".$data['id']."witch$rand' title='".__('Mostrar avisos p&uacute;blicos')."'>
               <input type='hidden' name='".$data['id']."' value='0'>
                              <input type='checkbox' id='".$data['id']."witch$rand' name='".$data['id']."' value='1'".
                     ((isset($rights['rights'])&& ($rights['rights']==4))?1:0
                        ? "checked='checked'"
                        : "")."
               >
               <span class='lever'></span>
            </label>
         </span>";

		  
         //   Dropdown::showYesNo($data['id'], (isset($rights['rights'])&& ($rights['rights']==4))?1:0);
         echo "</td></tr>\n";
      }

      if ($canedit) {
         echo "<tr class='tab_bg_1'><td colspan='2' class='center'>";
         echo "<input type='hidden' name='aviso' value='$avisos_id'>";
         echo "<input type='submit' name='update' value='"._sx('button', 'Update')."' ".
                "class='submit'>";
         echo "</td></tr>\n";
         echo "</table>\n";
         Html::closeForm();
      } else {
         echo "</table>\n";
      }
   }


   
   /**
    * @param $avisos
   **/
   static function updateForAviso($avisos) {
      global $DB;
	  
	  $delete = "delete from glpi_profilerights where name='plugin_avisos_aviso_".$avisos['aviso']."'";	  
	  $result = $DB->query($delete);
	  
	  foreach ($avisos as $key => $val) {
		if (($key!= 'aviso') && ($key!= 'update') && ($key!= '_glpi_csrf_token')){
			if ($val == 1){
				$right = 4;
			} else {
				$right = 0;
			}
			$update = "INSERT INTO glpi_profilerights (name, rights, profiles_id)
				 VALUES ('plugin_avisos_aviso_".$avisos['aviso']."',".$right.",".$key.");";
			$result = $DB->query($update);
		}		 	  
	  }
	return true;
	
   }
   
}
?>