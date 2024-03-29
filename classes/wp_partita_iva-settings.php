<?php

if ( ! class_exists( 'wp_partita_iva_Settings' ) ) {

	/**
	 * Handles plugin settings and user profile meta fields
	 */
	class wp_partita_iva_Settings extends wp_partita_iva_Module {
		protected $settings;
		protected static $default_settings;
		protected static $readable_properties  = array( 'settings' );
		protected static $writeable_properties = array( 'settings' );

		const REQUIRED_CAPABILITY = 'administrator';


		/*
		 * General methods
		 */

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		protected function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Public setter for protected variables
		 *
		 * Updates settings outside of the Settings API or other subsystems
		 *
		 * @mvc Controller
		 *
		 * @param string $variable
		 * @param array  $value This will be merged with wp_partita_iva_Settings->settings, so it should mimic the structure of the wp_partita_iva_Settings::$default_settings. It only needs the contain the values that will change, though. See WordPress_Partita_IVA->upgrade() for an .
		 */
		public function __set( $variable, $value ) {
			// Note: wp_partita_iva_Module::__set() is automatically called before this

			if ( $variable != 'settings' ) {
				return;
			}
            print_r($variable . $value);
			$this->settings = self::validate_settings( $value );
			update_option( 'wp_partita_iva_settings', $this->settings );
		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @mvc Controller
		 */
		public function register_hook_callbacks() {
			add_action( 'admin_menu',               __CLASS__ . '::register_settings_pages' );
			add_action( 'show_user_profile',        __CLASS__ . '::add_user_fields' );
			add_action( 'edit_user_profile',        __CLASS__ . '::add_user_fields' );
			add_action( 'personal_options_update',  __CLASS__ . '::save_user_fields' );
			add_action( 'edit_user_profile_update', __CLASS__ . '::save_user_fields' );

			add_action( 'init',                     array( $this, 'init' ) );
			add_action( 'admin_init',               array( $this, 'register_settings' ) );

			add_filter(
				'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) ) . '/bootstrap.php',
				__CLASS__ . '::add_plugin_action_links'
			);
		}

		/**
		 * Prepares site to use the plugin during activation
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @mvc Controller
		 */
		public function deactivate() {
		}

		/**
		 * Initializes variables
		 *
		 * @mvc Controller
		 */
		public function init() {
			self::$default_settings = self::get_default_settings();
			$this->settings         = self::get_settings();
		}

		/**
		 * Executes the logic of upgrading from specific older versions of the plugin to the current version
		 *
		 * @mvc Model
		 *
		 * @param string $db_version
		 */
		public function upgrade( $db_version = 0 ) {
			/*
			if( version_compare( $db_version, 'x.y.z', '<' ) )
			{
				// Do stuff
			}
			*/
		}

		/**
		 * Checks that the object is in a correct state
		 *
		 * @mvc Model
		 *
		 * @param string $property An individual property to check, or 'all' to check all of them
		 * @return bool
		 */
		protected function is_valid( $property = 'all' ) {
			// Note: __set() calls validate_settings(), so settings are never invalid

			return true;
		}


		/*
		 * Plugin Settings
		 */

		/**
		 * Establishes initial values for all settings
		 *
		 * @mvc Model
		 *
         * @return array
         */
        protected static function get_default_settings()
        {
            $basic = array(
                'field-cf' => 0,
                'field-pi' => 0,
                'field-nin' => 0,
                'field-pec' => 0
            );

			$advanced = array(
                'field-obb-cf' => 0,
                'field-obb-pi' => 0,
                'field-obb-nin' => 0,
                'field-obb-pec' => 0
			);

			return array(
				'db-version' => '0',
				'basic'      => $basic,
				'advanced'   => $advanced
			);
		}

		/**
		 * Retrieves all of the settings from the database
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		protected static function get_settings() {
			$settings = shortcode_atts(
				self::$default_settings,
				get_option( 'wp_partita_iva_settings', array() )
			);

			return $settings;
		}

		/**
		 * Adds links to the plugin's action link section on the Plugins page
		 *
		 * @mvc Model
		 *
		 * @param array $links The links currently mapped to the plugin
		 * @return array
		 */
		public static function add_plugin_action_links( $links ) {
			array_unshift( $links, '<a href="http://wordpress.org/extend/plugins/wordpress_partita_iva/faq/">Help</a>' );
			array_unshift( $links, '<a href="options-general.php?page=' . 'wp_partita_iva_settings">Settings</a>' );

			return $links;
		}

		/**
		 * Adds pages to the Admin Panel menu
		 *
		 * @mvc Controller
		 */
		public static function register_settings_pages() {
			add_submenu_page(
				'options-general.php',
				wp_partita_iva_NAME . ' Settings',
				wp_partita_iva_NAME,
				self::REQUIRED_CAPABILITY,
				'wp_partita_iva_settings',
				__CLASS__ . '::markup_settings_page'
			);
		}

		/**
		 * Creates the markup for the Settings page
		 *
		 * @mvc Controller
		 */
		public static function markup_settings_page() {
			if ( current_user_can( self::REQUIRED_CAPABILITY ) ) {
				echo self::render_template( 'wp_partita_iva-settings/page-settings.php' );
			} else {
				wp_die( 'Access denied.' );
			}
		}

		/**
		 * Registers settings sections, fields and settings
		 *
		 * @mvc Controller
		 */
		public function register_settings() {
			/*
			 * Basic Section
			 */
			add_settings_section(
				'wp_partita_iva_section-basic',
				'Abilitare i campi',
				__CLASS__ . '::markup_section_headers',
				'wp_partita_iva_settings'
			);

            add_settings_field(
				'wp_partita_iva_field-cf',
				'Codice Fiscale',
				array( $this, 'markup_fields' ),
				'wp_partita_iva_settings',
				'wp_partita_iva_section-basic',
				array( 'label_for' => 'wp_partita_iva_field-cf' )
			);
            add_settings_field(
                'wp_partita_iva_field-pi',
                'Partita IVA',
                array( $this, 'markup_fields' ),
                'wp_partita_iva_settings',
                'wp_partita_iva_section-basic',
                array( 'label_for' => 'wp_partita_iva_field-pi' )
            );
            add_settings_field(
                'wp_partita_iva_field-nin',
                'Codice Univoco',
                array( $this, 'markup_fields' ),
                'wp_partita_iva_settings',
                'wp_partita_iva_section-basic',
                array( 'label_for' => 'wp_partita_iva_field-nin' )
            );
            add_settings_field(
                'wp_partita_iva_field-pec',
                'PEC',
                array( $this, 'markup_fields' ),
                'wp_partita_iva_settings',
                'wp_partita_iva_section-basic',
                array( 'label_for' => 'wp_partita_iva_field-pec' )
            );

			/*
			 * Advanced Section
			 */
			add_settings_section(
                'wp_partita_iva_section-advanced',
                'Impostazioni di obbligatorietà dei campi',
                __CLASS__ . '::markup_section_headers',
                'wp_partita_iva_settings'
            );
			add_settings_field(
			    'wp_partita_iva_field-obb-cf',
				'Codice Fiscale',
				array( $this, 'markup_fields' ),
				'wp_partita_iva_settings',
				'wp_partita_iva_section-advanced',
				array( 'label_for' => 'wp_partita_iva_field-obb-cf' )
			);
            add_settings_field(
                'wp_partita_iva_field-obb-pi',
                'Partita IVA',
                array( $this, 'markup_fields' ),
                'wp_partita_iva_settings',
                'wp_partita_iva_section-advanced',
                array( 'label_for' => 'wp_partita_iva_field-obb-pi' )
            );
            add_settings_field(
                'wp_partita_iva_field-obb-nin',
                'Codice Univoco',
                array( $this, 'markup_fields' ),
                'wp_partita_iva_settings',
                'wp_partita_iva_section-advanced',
                array( 'label_for' => 'wp_partita_iva_field-obb-nin' )
            );
            add_settings_field(
                'wp_partita_iva_field-obb-pec',
                'PEC',
                array( $this, 'markup_fields' ),
                'wp_partita_iva_settings',
                'wp_partita_iva_section-advanced',
                array( 'label_for' => 'wp_partita_iva_field-obb-pec' )
            );


			// The settings container
			register_setting(
				'wp_partita_iva_settings',
				'wp_partita_iva_settings',
				array( $this, 'validate_settings' )
			);
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @mvc Controller
		 *
		 * @param array $section
		 */
		public static function markup_section_headers( $section ) {
			echo self::render_template( 'wp_partita_iva-settings/page-settings-section-headers.php', array( 'section' => $section ), 'always' );
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @mvc Controller
		 *
		 * @param array $field
		 */
        public function markup_fields($field)
        {
            switch ($field['label_for']) {
                case 'wp_partita_iva_field-cf':
//print_r();
                    break;
                case 'wp_partita_iva_field-pi':
                    // Do any extra processing here
                    break;
                case 'wp_partita_iva_field-nin':
                    break;
                case 'wp_partita_iva_field-pec':
                    break;
            }

            echo self::render_template('wp_partita_iva-settings/page-settings-fields.php', array('settings' => $this->settings, 'field' => $field), 'always');
        }

		/**
		 * Validates submitted setting values before they get saved to the database. Invalid data will be overwritten with defaults.
		 *
		 * @mvc Model
		 *
		 * @param array $new_settings
		 * @return array
		 */
		public function validate_settings( $new_settings ) {
			$new_settings = shortcode_atts( $this->settings, $new_settings );
            // print_r($new_settings);

            if (!is_string($new_settings['db-version'])) {
                $new_settings['db-version'] = WordPress_Partita_IVA::VERSION;
            }
            if (!is_string($new_settings['basic']['field-cf'])) {
                // $new_settings['basic']['field-cf'] = WordPress_Partita_IVA::AB_CF;
            }
            if (!is_string($new_settings['basic']['field-pi'])) {
                //   $new_settings['basic']['field-pi'] = WordPress_Partita_IVA::AB_PI;
            }
            if (!is_string($new_settings['basic']['field-nin'])) {
                // $new_settings['basic']['field-nin'] = WordPress_Partita_IVA::AB_NIN;
            }
            if (!is_string($new_settings['basic']['field-pec'])) {
                // $new_settings['basic']['field-pec'] = WordPress_Partita_IVA::AB_PEC;
            }
            if (!is_string($new_settings['basic']['field-obb-cf'])) {
                // $new_settings['basic']['field-obb-cf'] = WordPress_Partita_IVA::OBB_CF;
            }
            if (!is_string($new_settings['basic']['field-obb-pi'])) {
                // $new_settings['basic']['field-obb-pi'] = WordPress_Partita_IVA::OBB_PI;
            }
            if (!is_string($new_settings['basic']['field-obb-nin'])) {
                //$new_settings['basic']['field-obb-nin'] = WordPress_Partita_IVA::OBB_PEC;
            }
            if (!is_string($new_settings['basic']['field-obb-pec'])) {
                //$new_settings['basic']['field-obb-pec'] = WordPress_Partita_IVA::OBB_PEC;
            }
			return $new_settings;
		}


		/*
		 * User Settings
		 */

		/**
		 * Adds extra option fields to a user's profile
		 *
		 * @mvc Controller
		 *
		 * @param object
		 */
		public static function add_user_fields( $user ) {
			echo self::render_template( 'wp_partita_iva-settings/user-fields.php', array( 'user' => $user ) );
		}

		/**
		 * Validates and saves the values of extra user fields to the database
		 *
		 * @mvc Controller
		 *
		 * @param int $user_id
		 */
		public static function save_user_fields( $user_id ) {
		}

		/**
		 * Validates submitted user field values before they get saved to the database
		 *
		 * @mvc Model
		 *
		 * @param int   $user_id
		 * @param array $user_fields
		 * @return array
		 */
		public static function validate_user_fields( $user_fields ) {
			return $user_fields;
		}
	} // end wp_partita_iva_Settings
}