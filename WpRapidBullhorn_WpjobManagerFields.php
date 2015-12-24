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
if ( ! defined( 'ABSPATH' ) ) exit;

class Astoundify_Job_Manager_Fields {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Make sure only one instance is only running.
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param void
	 * @return object $instance The one true class instance.
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param void
	 * @return void
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Set some smart defaults to class variables.
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param void
	 * @return void
	 */
	private function setup_globals() {
		$this->file         = __FILE__;
		
		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file ); 
	}

	/**
	 * Hooks and filters.
	 *
	 * We need to hook into a couple of things:
	 * 1. Output fields on frontend, and save.
	 * 2. Output fields on backend, and save (done automatically).
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param void
	 * @return void
	 */
	private function setup_actions() {
		/**
		 * Filter the default fields that ship with WP Job Manager.
		 * The `form_fields` method is what we use to add our own custom fields.
		 */
		add_filter( 'submit_job_form_fields', array( $this, 'form_fields' ) );

		/**
		 * When WP Job Manager is saving all of the default field data, we need to also
		 * save our custom fields. The `update_job_data` callback is what does this.
		 */
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );

		/**
		 * Filter the default fields that are output in the WP admin when viewing a job listing.
		 * The `job_listing_data_fields` adds the same fields to the backend that we added to the front.
		 *
		 * We do not need to add an additional callback for saving the data, as this is done automatically.
		 */
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'job_listing_data_fields' ) );
	}

	/**
	 * Add fields to the submission form.
	 *
	 * Currently the fields must fall between two sections: "job" or "company". Until
	 * WP Job Manager filters the data that passes to the registration template, these are the
	 * only two sections we can manipulate.
	 *
	 * You may use a custom field type, but you will then need to filter the `job_manager_locate_template`
	 * to search in `/templates/form-fields/$type-field.php` in your theme or plugin.
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param array $fields The existing fields
	 * @return array $fields The modified fields
	 */
	function form_fields( $fields ) {
		$fields[ 'job' ][ 'job_address_city' ] = array(
			'label'       => 'City',  
			'type'        => 'text',       
			'placeholder' => 'City',    
			'required'    => false,          
			'priority'    => 3                
		);
		$fields[ 'job' ][ 'job_address_state' ] = array(
			'label'       => 'State',  
			'type'        => 'text',       
			'placeholder' => 'State',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_benefits' ] = array(
			'label'       => 'Benefits',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_educationDegree' ] = array(
			'label'       => 'Education Degree',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_feeArrangement' ] = array(
			'label'       => 'Fee Arrangement',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_hoursOfOperation' ] = array(
			'label'       => 'Hours Of Operation',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_hoursPerWeek' ] = array(
			'label'       => 'Hours Per Week',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_salary' ] = array(
			'label'       => 'Salary',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_salaryUnit' ] = array(
			'label'       => 'Salary Unit',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_taxRate' ] = array(
			'label'       => 'Tax Rate',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_taxStatus' ] = array(
			'label'       => 'Tax Status',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_travelRequirements' ] = array(
			'label'       => 'Trave lRequirements',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		$fields[ 'job' ][ 'job_willRelocate' ] = array(
			'label'       => 'Will Relocate',  
			'type'        => 'text',       
			'placeholder' => '',     
			'required'    => false,             
			'priority'    => 3                
		);
		
		/**
		 * Repeat this for any additional fields.
		 */

		return $fields;
	}

	/**
	 * When the form is submitted, update the data.
	 *
	 * All data is stored in the $values variable that is in the same
	 * format as the fields array.
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param int $job_id The ID of the job being submitted.
	 * @param array $values The values of each field.
	 * @return void
	 */
	function update_job_data( $job_id, $values ) {
		
		$jobOrderID = isset ( $values[ 'job' ][ 'job_jobOrderID' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_jobOrderID' ] ) : null;
		if ( $jobOrderID )
			update_post_meta( $job_id, '_job_jobOrderID', $jobOrderID );	
		
		$address_city = isset ( $values[ 'job' ][ 'job_address_city' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_address_city' ] ) : null;
		if ( $address_city )
			update_post_meta( $job_id, '_job_address_city', $address_city );		
		
		$address_state = isset ( $values[ 'job' ][ 'job_address_state' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_address_state' ] ) : null;
		if ( $address_state )
			update_post_meta( $job_id, '_job_address_state', $address_state );
			
		$benefits = isset ( $values[ 'job' ][ 'job_benefits' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_benefits' ] ) : null;
		if ( $benefits )
			update_post_meta( $job_id, '_job_benefits', $benefits );	
		
		$educationDegree = isset ( $values[ 'job' ][ 'job_educationDegree' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_educationDegree' ] ) : null;
		if ( $educationDegree )
			update_post_meta( $job_id, '_job_educationDegree', $educationDegree );	
		
		$feeArrangement = isset ( $values[ 'job' ][ 'job_feeArrangement' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_feeArrangement' ] ) : null;
		if ( $feeArrangement )
			update_post_meta( $job_id, '_job_feeArrangement', $feeArrangement );
			
		$hoursOfOperation = isset ( $values[ 'job' ][ 'job_hoursOfOperation' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_hoursOfOperation' ] ) : null;
		if ( $hoursOfOperation )
			update_post_meta( $job_id, '_job_hoursOfOperation', $hoursOfOperation );
		
		$hoursPerWeek = isset ( $values[ 'job' ][ 'job_hoursPerWeek' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_hoursPerWeek' ] ) : null;
		if ( $hoursPerWeek )
			update_post_meta( $job_id, '_job_hoursPerWeek', $hoursPerWeek );	
			
		$salary = isset ( $values[ 'job' ][ 'job_salary' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_salary' ] ) : null;
		if ( $salary )
			update_post_meta( $job_id, '_job_salary', $salary );
		
		$salaryUnit = isset ( $values[ 'job' ][ 'job_salaryUnit' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_salaryUnit' ] ) : null;
		if ( $salaryUnit )
			update_post_meta( $job_id, '_job_salaryUnit', $salaryUnit );
		
		$taxRate = isset ( $values[ 'job' ][ 'job_taxRate' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_taxRate' ] ) : null;
		if ( $taxRate )
			update_post_meta( $job_id, '_job_taxRate', $taxRate );
		
		$taxStatus = isset ( $values[ 'job' ][ 'job_taxStatus' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_taxStatus' ] ) : null;
		if ( $taxStatus )
			update_post_meta( $job_id, '_job_taxStatus', $taxStatus );
		
		$travelRequirements = isset ( $values[ 'job' ][ 'job_travelRequirements' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_travelRequirements' ] ) : null;
		if ( $travelRequirements )
			update_post_meta( $job_id, '_job_travelRequirements', $travelRequirements );
			
		$willRelocate = isset ( $values[ 'job' ][ 'job_willRelocate' ] ) ? sanitize_text_field( $values[ 'job' ][ 'job_willRelocate' ] ) : null;
		if ( $willRelocate )
			update_post_meta( $job_id, '_job_willRelocate', $willRelocate );		
		}

	/**
	 * Add fields to the admin write panel.
	 *
	 * There is a slight disconnect between the frontend and backend at the moment.
	 * The frontend allows for select boxes, but there is no way to output those in
	 * the admin panel at the moment.
	 *
	 * @since Custom fields for WP Job Manager 1.0
	 *
	 * @param array $fields The existing fields
	 * @return array $fields The modified fields
	 */
	function job_listing_data_fields( $fields ) {
		/**
		 * Add the field we added to the frontend, using the meta key as the name of the
		 * field. We do not need to separate these fields into "job" or "company" as they
		 * are all output in the same spot.
		 */
		
		$fields[ '_job_jobOrderID' ] = array(
			'label'       => 'Bullhorn jobOrderID', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		 
		$fields[ '_job_address_city' ] = array(
			'label'       => 'City', // The field label
			'placeholder' => 'City',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3      
		);
		$fields[ '_job_address_state' ] = array(
			'label'       => 'State', // The field label
			'placeholder' => 'State',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_benefits' ] = array(
			'label'       => 'Benefits', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_educationDegree' ] = array(
			'label'       => 'Education Degree', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_feeArrangement' ] = array(
			'label'       => 'Fee Arrangement', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_hoursOfOperation' ] = array(
			'label'       => 'Hours Of Operation', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_hoursPerWeek' ] = array(
			'label'       => 'Hours Per Week', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_salary' ] = array(
			'label'       => 'Salary', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_salaryUnit' ] = array(
			'label'       => 'Salary Unit', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_taxRate' ] = array(
			'label'       => 'Tax Rate', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_taxStatus' ] = array(
			'label'       => 'Tax Status', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_travelRequirements' ] = array(
			'label'       => 'Travel Requirements', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		$fields[ '_job_willRelocate' ] = array(
			'label'       => 'Will Relocate', // The field label
			'placeholder' => '',     // The default value when adding via backend.
			'type'        => 'text',
			'required'    => false,          
			'priority'    => 3     
		);
		/**
		 * Repeat this for any additional fields.
		 */

		return $fields;
	}
}
add_action( 'init', array( 'Astoundify_Job_Manager_Fields', 'instance' ) );