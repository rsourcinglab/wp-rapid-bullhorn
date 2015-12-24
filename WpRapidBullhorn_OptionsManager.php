<?php
/*
	Copyright 2015  Rapid Sourcing Lab  
	WP Rapid Bullhorn Plugin :you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	WordPress Plugin Template is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Contact Form to Database Extension.
	If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

class WpRapidBullhorn_OptionsManager {

    public function getOptionNamePrefix() {
        return get_class($this) . '_';
    }


    /**
     * Define your options meta data here as an array, where each element in the array
     * @return array of key=>display-name and/or key=>array(display-name, choice1, choice2, ...)
     * key: an option name for the key (this name will be given a prefix when stored in
     * the database to ensure it does not conflict with other plugin options)
     * value: can be one of two things:
     *   (1) string display name for displaying the name of the option to the user on a web page
     *   (2) array where the first element is a display name (as above) and the rest of
     *       the elements are choices of values that the user can select
     * e.g.
     * array(
     *   'item' => 'Item:',             // key => display-name
     *   'rating' => array(             // key => array ( display-name, choice1, choice2, ...)
     *       'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber'),
     *       'Rating:', 'Excellent', 'Good', 'Fair', 'Poor')
     */
    public function getOptionMetaData() {
        return array();
    }

    /**
     * @return array of string name of options
     */
    public function getOptionNames() {
        return array_keys($this->getOptionMetaData());
    }

    /**
     * Override this method to initialize options to default values and save to the database with add_option
     * @return void
     */
    protected function initOptions() {
    }

    /**
     * Cleanup: remove all options from the DB
     * @return void
     */
    protected function deleteSavedOptions() {
        $optionMetaData = $this->getOptionMetaData();
        if (is_array($optionMetaData)) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                $prefixedOptionName = $this->prefix($aOptionKey); // how it is stored in DB
                delete_option($prefixedOptionName);
            }
        }
    }

    /**
     * @return string display name of the plugin to show as a name/title in HTML.
     * Just returns the class name. Override this method to return something more readable
     */
    public function getPluginDisplayName() {
        return get_class($this);
    }

    /**
     * Get the prefixed version input $name suitable for storing in WP options
     * Idempotent: if $optionName is already prefixed, it is not prefixed again, it is returned without change
     * @param  $name string option name to prefix. Defined in settings.php and set as keys of $this->optionMetaData
     * @return string
     */
    public function prefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }

    /**
     * Remove the prefix from the input $name.
     * Idempotent: If no prefix found, just returns what was input.
     * @param  $name string
     * @return string $optionName without the prefix.
     */
    public function &unPrefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) {
            return substr($name, strlen($optionNamePrefix));
        }
        return $name;
    }

    /**
     * A wrapper function delegating to WP get_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param $default string default value to return if the option is not set
     * @return string the value from delegated call to get_option(), or optional default value
     * if option is not set.
     */
    public function getOption($optionName, $default = null) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }

    /**
     * A wrapper function delegating to WP delete_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @return bool from delegated call to delete_option()
     */
    public function deleteOption($optionName) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return delete_option($prefixedOptionName);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function addOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return add_option($prefixedOptionName, $value);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function updateOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return update_option($prefixedOptionName, $value);
    }

    /**
     * A Role Option is an option defined in getOptionMetaData() as a choice of WP standard roles, e.g.
     * 'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber')
     * The idea is use an option to indicate what role level a user must minimally have in order to do some operation.
     * So if a Role Option 'CanDoOperationX' is set to 'Editor' then users which role 'Editor' or above should be
     * able to do Operation X.
     * Also see: canUserDoRoleOption()
     * @param  $optionName
     * @return string role name
     */
    public function getRoleOption($optionName) {
        $roleAllowed = $this->getOption($optionName);
        if (!$roleAllowed || $roleAllowed == '') {
            $roleAllowed = 'Administrator';
        }
        return $roleAllowed;
    }

    /**
     * Given a WP role name, return a WP capability which only that role and roles above it have
     * http://codex.wordpress.org/Roles_and_Capabilities
     * @param  $roleName
     * @return string a WP capability or '' if unknown input role
     */
    protected function roleToCapability($roleName) {
        switch ($roleName) {
            case 'Super Admin':
                return 'manage_options';
            case 'Administrator':
                return 'manage_options';
            case 'Editor':
                return 'publish_pages';
            case 'Author':
                return 'publish_posts';
            case 'Contributor':
                return 'edit_posts';
            case 'Subscriber':
                return 'read';
            case 'Anyone':
                return 'read';
        }
        return '';
    }

    /**
     * @param $roleName string a standard WP role name like 'Administrator'
     * @return bool
     */
    public function isUserRoleEqualOrBetterThan($roleName) {
        if ('Anyone' == $roleName) {
            return true;
        }
        $capability = $this->roleToCapability($roleName);
        return current_user_can($capability);
    }

    /**
     * @param  $optionName string name of a Role option (see comments in getRoleOption())
     * @return bool indicates if the user has adequate permissions
     */
    public function canUserDoRoleOption($optionName){
        $roleAllowed = $this->getRoleOption($optionName);
        if ('Anyone' == $roleAllowed) {
            return true;
        }
        return $this->isUserRoleEqualOrBetterThan($roleAllowed);
    }

    /**
     * see: http://codex.wordpress.org/Creating_Options_Pages
     * @return void
     */
    public function createSettingsMenu() {
        $pluginName = $this->getPluginDisplayName();
        //create new top-level menu
        add_menu_page($pluginName . ' Plugin Settings',
                      $pluginName,
                      'administrator',
                      get_class($this),
                      array(&$this, 'settingsPage')
        /*,plugins_url('/images/icon.png', __FILE__)*/); // if you call 'plugins_url; be sure to "require_once" it

        //call register settings function
        add_action('admin_init', array(&$this, 'registerSettings'));
    }

    public function registerSettings() {
        $settingsGroup = get_class($this) . '-settings-group';
        $optionMetaData = $this->getOptionMetaData();
        foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
            register_setting($settingsGroup, $aOptionMeta);
        }
    }

    /**
     * Creates HTML for the Administration page to set options for this plugin.
     * Override this method to create a customized page.
     * @return void
     */
    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wp-rapid-bullhorn'));
        }

        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, $_POST[$aOptionKey]);
                }
            }
        }

        // HTML for the page
        $settingsGroup = get_class($this) . '-settings-group';
        ?>
        <div class="wrap">
            <h2><?php _e('System Settings', 'wp-rapid-bullhorn'); ?></h2>
            <table cellspacing="1" cellpadding="2"><tbody>
            <tr><td><?php _e('System', 'wp-rapid-bullhorn'); ?></td><td><?php echo php_uname(); ?></td></tr>
            <tr><td><?php _e('PHP Version', 'wp-rapid-bullhorn'); ?></td>
                <td><?php echo phpversion(); ?>
                <?php
                if (version_compare('5.2', phpversion()) > 0) {
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    _e('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'wp-rapid-bullhorn');
                    echo '</span>';
                }
                ?>
                </td>
            </tr>
            <tr><td><?php _e('MySQL Version', 'wp-rapid-bullhorn'); ?></td>
                <td><?php echo $this->getMySqlVersion() ?>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    if (version_compare('5.0', $this->getMySqlVersion()) > 0) {
                        _e('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'wp-rapid-bullhorn');
                    }
                    echo '</span>';
                    ?>
                </td>
            </tr>
            </tbody></table>

            <h2><?php echo $this->getPluginDisplayName(); echo ' '; _e('Settings', 'wp-rapid-bullhorn'); ?></h2>

            <form method="post" action="">
            <?php settings_fields($settingsGroup); ?>
                <style type="text/css">
                    table.plugin-options-table {width: 100%; padding: 0;}
                    table.plugin-options-table tr:nth-child(even) {background: #f9f9f9}
                    table.plugin-options-table tr:nth-child(odd) {background: #FFF}
                    table.plugin-options-table tr:first-child {width: 35%;}
                    table.plugin-options-table td {vertical-align: middle;}
                    table.plugin-options-table td+td {width: auto}
                    table.plugin-options-table td > p {margin-top: 0; margin-bottom: 0;}
                </style>
                <table class="plugin-options-table"><tbody>
                <?php
                if ($optionMetaData != null) {
                    foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                        $displayText = is_array($aOptionMeta) ? $aOptionMeta[0] : $aOptionMeta;
                        ?>
                            <tr valign="top">
                                <th scope="row"><p><label for="<?php echo $aOptionKey ?>"><?php echo $displayText ?></label></p></th>
                                <td>
                                <?php $this->createFormControl($aOptionKey, $aOptionMeta, $this->getOption($aOptionKey)); ?>
                                </td>
                            </tr>
                        <?php
                    }
                }
                ?>
                </tbody></table>
                <p class="submit">
                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', 'wp-rapid-bullhorn') ?>"/>
                </p>
            </form>
        </div>
        <?php

    }

    /**
     * Helper-function outputs the correct form element (input tag, select tag) for the given item
     * @param  $aOptionKey string name of the option (un-prefixed)
     * @param  $aOptionMeta mixed meta-data for $aOptionKey (either a string display-name or an array(display-name, option1, option2, ...)
     * @param  $savedOptionValue string current value for $aOptionKey
     * @return void
     */
    protected function createFormControl($aOptionKey, $aOptionMeta, $savedOptionValue) {
        if (is_array($aOptionMeta) && count($aOptionMeta) >= 2) { // Drop-down list
            $choices = array_slice($aOptionMeta, 1);
            ?>
            <p><select name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>">
            <?php
                            foreach ($choices as $aChoice) {
                $selected = ($aChoice == $savedOptionValue) ? 'selected' : '';
                ?>
                    <option value="<?php echo $aChoice ?>" <?php echo $selected ?>><?php echo $this->getOptionValueI18nString($aChoice) ?></option>
                <?php
            }
            ?>
            </select></p>
            <?php

        }
        else { // Simple input field
            ?>
            <p><input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>"
                      value="<?php echo esc_attr($savedOptionValue) ?>" size="50"/></p>
            <?php

        }
    }

    /**
     * Override this method and follow its format.
     * The purpose of this method is to provide i18n display strings for the values of options.
     * For example, you may create a options with values 'true' or 'false'.
     * In the options page, this will show as a drop down list with these choices.
     * But when the the language is not English, you would like to display different strings
     * for 'true' and 'false' while still keeping the value of that option that is actually saved in
     * the DB as 'true' or 'false'.
     * To do this, follow the convention of defining option values in getOptionMetaData() as canonical names
     * (what you want them to literally be, like 'true') and then add each one to the switch statement in this
     * function, returning the "__()" i18n name of that string.
     * @param  $optionValue string
     * @return string __($optionValue) if it is listed in this method, otherwise just returns $optionValue
     */
    protected function getOptionValueI18nString($optionValue) {
        switch ($optionValue) {
            case 'true':
                return __('true', 'wp-rapid-bullhorn');
            case 'false':
                return __('false', 'wp-rapid-bullhorn');

            case 'Administrator':
                return __('Administrator', 'wp-rapid-bullhorn');
            case 'Editor':
                return __('Editor', 'wp-rapid-bullhorn');
            case 'Author':
                return __('Author', 'wp-rapid-bullhorn');
            case 'Contributor':
                return __('Contributor', 'wp-rapid-bullhorn');
            case 'Subscriber':
                return __('Subscriber', 'wp-rapid-bullhorn');
            case 'Anyone':
                return __('Anyone', 'wp-rapid-bullhorn');
        }
        return $optionValue;
    }

    /**
     * Query MySQL DB for its version
     * @return string|false
     */
    protected function getMySqlVersion() {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
             return $rows[0]->mysqlversion;
        }
        return false;
    }
	
	


    /**
     * If you want to generate an email address like "no-reply@your-site.com" then
     * you can use this to get the domain name part.
     * E.g.  'no-reply@' . $this->getEmailDomain();
     * This code was stolen from the wp_mail function, where it generates a default
     * from "wordpress@your-site.com"
     * @return string domain name
     */
    public function getEmailDomain() {
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        return $sitename;
    }

/**
     *Function to Bullhorn Data Post
	 */	
	protected function saveBullhornDatan() {
        global $wpdb;
		
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
             return $rows[0]->mysqlversion;
        }
        return false;
    }
	
	/**
     *Function to Import Bullhorn Data
	 */
	
	  	
	public function importBullhornPage() {
		global $wpdb;
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.', 'wp-rapid-bullhorn'));
			}
	
			$optionMetaData = $this->getOptionMetaData();
			?>
            <div class="wrap">
            <h2><?php _e('System Settings', 'wp-rapid-bullhorn'); ?></h2>
            <table cellspacing="1" cellpadding="2"><tbody>
            <tr><td><?php _e('System', 'wp-rapid-bullhorn'); ?></td><td><?php echo php_uname(); ?></td></tr>
            <tr><td><?php _e('PHP Version', 'wp-rapid-bullhorn'); ?></td>
                <td><?php echo phpversion(); ?>
                <?php
                if (version_compare('5.2', phpversion()) > 0) {
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    _e('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'wp-rapid-bullhorn');
                    echo '</span>';
                }
                ?>
                </td>
            </tr>
            <tr><td><?php _e('MySQL Version', 'wp-rapid-bullhorn'); ?></td>
                <td><?php echo $this->getMySqlVersion() ?>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    if (version_compare('5.0', $this->getMySqlVersion()) > 0) {
                        _e('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'wp-rapid-bullhorn');
                    }
                    echo '</span>';
                    ?>
                </td>
            </tr>
            </tbody></table>

            <h2><?php echo $this->getPluginDisplayName(); echo ' '; _e('Importer', 'wp-rapid-bullhorn'); ?></h2>
            
            <?php 
			
			$params = array(
			'trace' => 1,
			'soap_version' => SOAP_1_1);
			$BHclient = new SoapClient("https://api.bullhornstaffing.com/webservices-1.1/?wsdl",$params);
			
			// To run this code, you will need a valid username, password, and API key.
			$username = $this->getOption('bh_username');
			$password = $this->getOption('bh_pass');
			$apiKey = $this->getOption('bh_apikey');
			$session_request = new stdClass();
			$session_request->username = $username;
			$session_request->password = $password;
			$session_request->apiKey = $apiKey;
			$API_session = $BHclient->startSession($session_request);
			$API_currentSession = $API_session->return;
			
			
			// Create an array with the query parameters
		$query_array = array(
		'entityName' => 'JobOrder',
		'where' => 'isOpen=1',
		'parameters' => array(),
        'distinct' => TRUE
		);
		
		// Create the DTO type that the API will understand by casting the array to the dtoQuery
		// type that the query operation expects. 
		$SOAP_query = new SoapVar($query_array, SOAP_ENC_OBJECT,"dtoQuery", "http://query.apiservice.bullhorn.com/");
		
		// Put the DTO into a request object
		$request_array = array (
		'session' => $API_currentSession,
		'query' => $SOAP_query);
		
		// Cast the request as a query type
		$SOAP_request = new SoapVar($request_array, SOAP_ENC_OBJECT, "query", "http://query.apiservice.bullhorn.com/");
		
		// Use the query method to return the candidate ids
		try {
		$queryResult = $BHclient->query($SOAP_request);
		} catch (SoapFault $fault) {
		var_dump($BHclient->__getLastRequest());
		die($fault->faultstring);
		}
		
		
		
		// Use the find() method to retrieve the candidate DTO for each Id
		// Loop through each Id in the query result list
		foreach ($queryResult->return->ids as $value){
			// Cast each Id to an integer type
			$findId = new SoapVar($value, XSD_INTEGER,"int","http://www.w3.org/2001/XMLSchema");
			// Create the find() method request
			$find_request = array(
				'session' => $API_currentSession,
				'entityName' => 'JobOrder',
				'id' => $findId
			);
			
			try {
			$findResult = $BHclient->find($find_request);
			} catch (SoapFault $fault) {
			var_dump($BHclient->__getLastRequest());
			die($fault->faultstring);
			}
	
		}
		
		
		
		// Use the findMultiple() method to retrieve the candidate DTO for each Id
		// Create an array containing each Id in the query result list
		
		$findId_array =  array();
		foreach ($queryResult->return->ids as $value){
			// Cast each Id to an integer type
			$findId = new SoapVar($value, XSD_INTEGER,"int","http://www.w3.org/2001/XMLSchema");
			$findId_array[] = $findId;
		}
		
		// Create the findMultiple() method request
		foreach(array_chunk($findId_array, 20) as $chunk)
        {
		
		    $find_request = array(
			'session' => $API_currentSession,
			'entityName' => 'JobOrder',
			'ids' => $chunk
		);
			
		// Use the findMultiple method to return the candidate dtos
		try {
			
			// echo "<pre>"  ;
			// print_R($find_request) ; 
			// die;
			
		$findjobs = $BHclient->findMultiple($find_request);
		
		 
		foreach($findjobs->return->dtos as $job)
		{
			
			
		   
		    
		   
		   
		   
		  $post = array(
						'post_status' => 'publish',
						'post_type' => 'job_listing',
						'post_title' => (string)$job->title,
						'post_content' => (string)$job->description,
						'post_date' => date("Y-m-d H:i:s", strtotime($job->dateAdded))
					);

					
		//	echo $job->employmentType;
		
		$posttag1 = $job->employmentType;
		
		$postmeta1 = array();
		$postmeta1 = array(
		  '_job_jobOrderID' => $job->jobOrderID,
		  '_company_description' => $job->publicDescription,
		  '_start_date' => strtotime($job->startDate),
		  '_date_end' => strtotime($job->dateEnd),
		  '_duration_weeks' => $job->durationWeeks,
		  '_employment_type'=> $job->employmentType,
		  '_custom_text_1' => $job->correlatedCustomInt1,
		  '_custom_text_2' => $job->correlatedCustomInt2,
		  '_custom_text_3' => $job->correlatedCustomInt3,
		  '_custom_text_4' => $job->correlatedCustomInt4,
		  '_custom_text_5' => $job->correlatedCustomInt5,
		  '_custom_text_6' => $job->correlatedCustomInt6,
		  '_custom_text_7' => $job->correlatedCustomInt7,
		  '_custom_text_8' => $job->correlatedCustomInt8,
		  '_custom_text_9' => $job->correlatedCustomInt9,
		  '_years_required' => $job->yearsRequired,
		  '_travel_requirements' => $job->travelRequirements,
		  '_certification_list' => $job->certificationList,
		  '_skill_list' => $job->skillList,
		  '_job_externalCategoryID' => $job->externalCategoryID,
		  '_job_benefits' => $job->benefits,
		  '_job_educationDegree' => $job->educationDegree,
		  '_job_hoursOfOperation' => $job->hoursOfOperation,
		  '_job_salary' => $job->salary,
		  '_job_salaryUnit' => $job->salaryUnit,
		  '_job_taxRate' => $job->taxRate,
		  '_job_taxStatus' => $job->taxStatus,
		  '_job_travelRequirements' => $job->travelRequirements,
		  '_job_willRelocate' => $job->willRelocate
		  );
		  
		$postmeta2 = array();
		foreach($job->address as $field_name => $field_value)
		{
		
			if(is_object($field_value))
			{
				$postmeta2['_job_location']=$field_value->address1;
				$postmeta2['_job_address_state']=$field_value->state;
				$postmeta2['_job_address_city']=$field_value->city;
			}
		
							
							
						   /*  if(is_object($field_value))
						    {
								
						        
									$postmeta2 = array(
									  '_job_location' => $field_value->address1,
									  '_job_address_city' => $field_value->city,
									  '_job_address_state' => $field_value->state
									  );
									 
									 $termregion = $field_value->state; 
								
								
							} */
		  }
			echo '</br>---------------------<br>';
		
		$post_id = $wpdb->get_var(
						sprintf("
							SELECT post_id
							FROM $wpdb->postmeta
							WHERE meta_key = '_job_jobOrderID'
							AND meta_value = %s
							LIMIT 1",
							$job->jobOrderID
						)
					);	
		
		if($post_id != 0)
					{
						$post['ID'] = $post_id;
						$post_id = wp_update_post($post);
					}
					else
					{
						$post_id = wp_insert_post($post);
					}
		
		//echo $termregion;
		/*$stterm = term_exists( $termregion ); 
		
		if(empty($stterm['term_id'])){
		wp_insert_term( 'job_listing_region', $termregion );
		$stterm = term_exists( $termregion ); 	
		}
		
		wp_set_post_terms( $post_id, $stterm['term_id'], 'job_listing_region' );
		
		
		$term = term_exists( $posttag1);
		var_dump($term);
		if(empty($term['term_id'])){
		wp_insert_term( 'job_listing_type', $posttag1 );
		$term = term_exists( $posttag1);;	
		}
		
		wp_set_post_terms( $post_id, $term['term_id'], 'job_listing_type' );*/
		
		$term = get_term_by('name', $termregion, 'job_listing_region');
		if($term->term_id==""){
			wp_insert_term(
			  $termregion, // the term 
			  'job_listing_region'
			);
		}
		
		$term = get_term_by('name', $termregion, 'job_listing_region');
		
		wp_set_post_terms( $post_id, $term->term_id, 'job_listing_region' );
		
		
		$tterm = get_term_by('name', $posttag1, 'job_listing_type');
		if($tterm->term_id==""){
			wp_insert_term(
			  $posttag1, 
			  'job_listing_type'
			);
		}
		
		$tterm = get_term_by('name', $posttag1, 'job_listing_type');
		wp_set_post_terms( $post_id, $tterm->term_id, 'job_listing_type' );
		$post_metas = array();
		$post_metas = array_merge($postmeta2,$postmeta1);
		
		foreach($post_metas as $pkey => $pvalue)
		{
		      @update_post_meta($post_id, $pkey, $pvalue);
		}
														
		/*ClientCorporation Query*/
		/*$cc_query_array = array(
		'entityName' => 'ClientCorporation',
		'parameters' => array()
		);
		
		$CCSOAP_query = new SoapVar($cc_query_array, SOAP_ENC_OBJECT,"dtoQuery", "http://query.apiservice.bullhorn.com/");
		
		$cc_request_array = array (
		'session' => $API_currentSession,
		'query' => $CCSOAP_query);
		
		// Cast the request as a query type
		$CCSOAP_request = new SoapVar($cc_request_array, SOAP_ENC_OBJECT, "query", "http://query.apiservice.bullhorn.com/");
		
		// Use the query method to return the candidate ids
		try {
		$ccqueryResult = $BHclient->query($CCSOAP_request);
		} catch (SoapFault $fault) {
		var_dump($BHclient->__getLastRequest());
		die($fault->faultstring);
		}
	
	//var_dump($ccqueryResult);	
	$ccfindId_array =  array();
	$ccfindId_array = (array)$ccqueryResult->return->ids;
		foreach(array_chunk($ccfindId_array, 20) as $ccchunk)
            {
		
		$ccfind_request = array(
			'session' => $API_currentSession,
			'entityName' => 'ClientCorporation',
			'ids' => $ccchunk
		);
			
	
		try {
		$findccs = $BHclient->findMultiple($ccfind_request);		
		} catch (SoapFault $fault) {
		var_dump($BHclient->__getLastRequest());
		die($fault->faultstring);
		}
		
		//var_dump($findccs);
		//echo '<br>I am here<br>';
	}*/
		/*-----------------*/
		}
		
		
		} catch (SoapFault $fault) {
		var_dump($BHclient->__getLastRequest());
		die($fault->faultstring);
		}
		}
	?>
		
	
        </div>
            
            <?php 
			
	}


}

