<?php

if ( ! class_exists( 'WordPress_Partita_IVA' ) ) {

	/**
	 * Main / front controller class
	 *
	 *
	 */
	class WordPress_Partita_IVA extends wp_partita_iva_Module {
		protected static $readable_properties  = array();    // These should really be constants, but PHP doesn't allow class constants to be arrays
		protected static $writeable_properties = array();
		protected $modules;

        const VERSION = '1.1';
        const PREFIX     = 'wp_partita_iva_';
        const AB_CF = '0';
        const AB_PI = '0';
        const AB_NIN = '0';
        const AB_PEC = '0';
        const OBB_CF = '0';
        const OBB_PI = '0';
        const OBB_NIN = '0';
        const OBB_PEC = '0';

        const DEBUG_MODE = true;


		/*
		 * Magic methods
		 */

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		protected function __construct() {
			$this->register_hook_callbacks();

			$this->modules = array(
				'wp_partita_iva_Settings'    => wp_partita_iva_Settings::get_instance(),
				'wp_partita_iva_Cron'        => wp_partita_iva_Cron::get_instance()
			);
		}


		/*
		 * Static methods
		 */

		/**
		 * Enqueues CSS, JavaScript, etc
		 *
		 * @mvc Controller
		 */
		public static function load_resources() {
			wp_register_script(
				self::PREFIX . 'wordpress_partita_iva',
				plugins_url( 'javascript/wordpress_partita_iva.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				self::VERSION,
				true
			);

			wp_register_style(
				self::PREFIX . 'admin',
				plugins_url( 'css/admin.css', dirname( __FILE__ ) ),
				array(),
				self::VERSION,
				'all'
			);

			if ( is_admin() ) {
				wp_enqueue_style( self::PREFIX . 'admin' );
			} else {
				wp_enqueue_script( self::PREFIX . 'wordpress_partita_iva' );
			}
		}

		/**
		 * Clears caches of content generated by caching plugins like WP Super Cache
		 *
		 * @mvc Model
		 */
		protected static function clear_caching_plugins() {
			// WP Super Cache
			if ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
			}

			// W3 Total Cache
			if ( class_exists( 'W3_Plugin_TotalCacheAdmin' ) ) {
				$w3_total_cache = w3_instance( 'W3_Plugin_TotalCacheAdmin' );

				if ( method_exists( $w3_total_cache, 'flush_all' ) ) {
					$w3_total_cache->flush_all();
				}
			}
		}


		/*
		 * Instance methods
		 */

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {
			if ( $network_wide && is_multisite() ) {
				$sites = wp_get_sites( array( 'limit' => false ) );

				foreach ( $sites as $site ) {
					switch_to_blog( $site['blog_id'] );
					$this->single_activate( $network_wide );
					restore_current_blog();
				}
			} else {
				$this->single_activate( $network_wide );
			}
		}

		/**
		 * Runs activation code on a new WPMS site when it's created
		 *
		 * @mvc Controller
		 *
		 * @param int $blog_id
		 */
		public function activate_new_site( $blog_id ) {
			switch_to_blog( $blog_id );
			$this->single_activate( true );
			restore_current_blog();
		}

		/**
		 * Prepares a single blog to use the plugin
		 *
		 * @mvc Controller
		 *
		 * @param bool $network_wide
		 */
		protected function single_activate( $network_wide ) {
			foreach ( $this->modules as $module ) {
				$module->activate( $network_wide );
			}

			flush_rewrite_rules();
		}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @mvc Controller
		 */
		public function deactivate() {
			foreach ( $this->modules as $module ) {
				$module->deactivate();
			}

			flush_rewrite_rules();
		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @mvc Controller
		 */
		public function register_hook_callbacks() {
			add_action( 'wp_enqueue_scripts',    __CLASS__ . '::load_resources' );
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::load_resources' );

			add_action( 'wpmu_new_blog',         array( $this, 'activate_new_site' ) );
			add_action( 'init',                  array( $this, 'init' ) );
			add_action( 'init',                  array( $this, 'upgrade' ), 11 );
		}

		/**
		 * Initializes variables
		 *
		 * @mvc Controller
		 */
		public function init() {
			try {
				$instance_ = new wp_partita_iva_Instance_Class( 'Instance ', '42' );
				//add_notice( $instance_->foo .' '. $instance_->bar );
			} catch ( Exception $exception ) {
				add_notice( __METHOD__ . ' error: ' . $exception->getMessage(), 'error' );
			}
		}

		/**
		 * Checks if the plugin was recently updated and upgrades if necessary
		 *
		 * @mvc Controller
		 *
		 * @param string $db_version
		 */
		public function upgrade( $db_version = 0 ) {
			if ( version_compare( $this->modules['wp_partita_iva_Settings']->settings['db-version'], self::VERSION, '==' ) ) {
				return;
			}

			foreach ( $this->modules as $module ) {
				$module->upgrade( $this->modules['wp_partita_iva_Settings']->settings['db-version'] );
			}

			$this->modules['wp_partita_iva_Settings']->settings = array( 'db-version' => self::VERSION );
			self::clear_caching_plugins();
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
			return true;
		}
    } // end WordPress_Partita_IVA


        /*    Imposto i  nuovi campi custom e li mostro nell'area personale dell'utente (billing address)
        */
        function wp_partita_iva_set_customer_billing_fields_in_profile( $fields )
        {
            $settings = get_option('wp_partita_iva_settings', array());

            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];

            if ($abilitazione_pi == 1) {
                $fields['billing_vat'] = array(
                    'label' => __('Partita IVA', 'woocommerce'),
                    'placeholder' => '',
                    'required' => false,
                    'clear' => false,
                    'type' => 'text',
                    'class' => array('form-row-wide')
                );
            }
            if ($abilitazione_cf == 1) {
                $fields['billing_cf'] = array(
                    'label' => __('Codice Fiscale', 'woocommerce'),
                    'placeholder' => '',
                    'required' => false,
                    'clear' => false,
                    'type' => 'text',
                    'class' => array('form-row-wide')
                );
            }
            if ($abilitazione_nin == 1) {
                $fields['billing_nin'] = array(
                    'label' => __('Codice Univoco', 'woocommerce'),
                    'placeholder' => '',
                    'required' => false,
                    'clear' => false,
                    'type' => 'text',
                    'class' => array('form-row-wide')
                );
            }
            if ($abilitazione_pec == 1) {
                $fields['billing_pec'] = array(
                    'label' => __('Indirizzo PEC', 'woocommerce'),
                    'placeholder' => '',
                    'required' => false,
                    'clear' => false,
                    'type' => 'text',
                    'class' => array('form-row-wide')
                );
            }
            return $fields;
        }

    add_filter('woocommerce_billing_fields', 'wp_partita_iva_set_customer_billing_fields_in_profile');


        /*    Mostro i campi custom nella pagina di checkout
        */
        function wp_partita_iva_set_customer_billing_fields_in_checkout( $fields )
        {
            $settings = get_option('wp_partita_iva_settings', array());

            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];
            $required_cf = $settings['advanced']['field-obb-cf'];
            $required_pi = $settings['advanced']['field-obb-pi'];
            $required_nin = $settings['advanced']['field-obb-nin'];
            $required_pec = $settings['advanced']['field-obb-pec'];


            if ($abilitazione_cf == 1) {
                $fields['billing']['billing_cf'] = array(
                    'label' => __('Codice Fiscale', 'woocommerce'),
                    'placeholder' => '',
                    'required' => $required_cf,
                    'clear' => false,
                    'type' => 'text',
                    'priority' => 32,
                    'class' => array('form-row-wide')
                );
            }
            if ($abilitazione_pi == 1) {
                $fields['billing']['billing_vat'] = array(
                    'label' => __('Partita IVA', 'woocommerce'),
                    'placeholder' => '',
                    'required' => $required_pi,
                    'clear' => false,
                    'type' => 'text',
                    'priority' => 33,
                    'class' => array('form-row-wide')
                );
            }
            if ($abilitazione_nin == 1) {
                $fields['billing']['billing_nin'] = array(
                    'label' => __('Codice Univoco', 'woocommerce'),
                    'placeholder' => '',
                    'required' => $required_nin,
                    'clear' => false,
                    'type' => 'text',
                    'priority' => 34,
                    'class' => array('form-row-wide')
                );
            }
            if ($abilitazione_pec == 1) {
                $fields['billing']['billing_pec'] = array(
                    'label' => __('Indirizzo PEC', 'woocommerce'),
                    'placeholder' => '',
                    'required' => $required_pec,
                    'clear' => false,
                    'type' => 'text',
                    'priority' => 35,
                    'class' => array('form-row-wide')
                );
            }
            $fields['billing']['billing_fatt'] = array(
                'label' => __('HO BISOGNO DELLA FATTURA ELETTRONICA', 'woocommerce'),
                'placeholder' => '',
                'required' => 0,
                'clear' => false,
                'type' => 'checkbox',
                'priority' => 31,
                'class' => array('form-row-wide')
            );
            return $fields;
        }
    add_filter('woocommerce_checkout_fields', 'wp_partita_iva_set_customer_billing_fields_in_checkout');


        /*    Mostro i campi custom nel profilo dell'utente lato amministrazione
        */
        function wp_partita_iva_set_customer_billing_fields_in_customer_profile_admin_side( $fields )
        {
            $settings = get_option('wp_partita_iva_settings', array());

            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];

            if ($abilitazione_pi == 1) {
                $fields['billing']['fields']['billing_vat'] = array(
                    'label' => __('Partita IVA', 'woocommerce'),
                    'description' => ''
                );
            }
            if ($abilitazione_cf == 1) {
                $fields['billing']['fields']['billing_cf'] = array(
                    'label' => __('Codice Fiscale', 'woocommerce'),
                    'description' => ''
                );
            }
            if ($abilitazione_nin == 1) {
                $fields['billing']['fields']['billing_nin'] = array(
                    'label' => __('Codice Univoco', 'woocommerce'),
                    'description' => ''
                );
            }
            if ($abilitazione_pec == 1) {
                $fields['billing']['fields']['billing_pec'] = array(
                    'label' => __('Indirizzo PEC', 'woocommerce'),
                    'description' => ''
                );
            }
            return $fields;
        }
    add_filter( 'woocommerce_customer_meta_fields', 'wp_partita_iva_set_customer_billing_fields_in_customer_profile_admin_side' );


        /*    Compilo con i dati del profilo (billing address) i campi custom nel checkout
        */
        function wp_partita_iva_populate_customer_billing_fields_in_checkout( $input, $key )
        {
            global $current_user;
            $settings = get_option('wp_partita_iva_settings', array());

            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];
            if ($abilitazione_pi == 1) {
                if ($key == 'billing_vat')
                    return get_user_meta($current_user->ID, 'billing_vat', true);
            }
            if ($abilitazione_cf == 1) {
                if ($key == 'billing_cf')
                    return get_user_meta($current_user->ID, 'billing_cf', true);
            }
            if ($abilitazione_nin == 1) {
                if ($key == 'billing_nin')
                    return get_user_meta($current_user->ID, 'billing_nin', true);
            }
            if ($abilitazione_pec == 1) {
                if ($key == 'billing_pec')
                    return get_user_meta($current_user->ID, 'billing_pec', true);
            }
            if ($key == 'billing_fatt')
                return get_user_meta($current_user->ID, 'billing_fatt', true);
        }
    add_filter('woocommerce_checkout_get_value', 'wp_partita_iva_populate_customer_billing_fields_in_checkout', 10, 2 );

        /*    Salvo le modifiche sui campi custom effettuate dall'area personale dell'utente (billing address)
        */
        function wp_partita_iva_store_customer_billing_fields_in_address( $user_id )
        {
            $settings = get_option('wp_partita_iva_settings', array());

            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];
            if ($abilitazione_pi == 1) {
                if (!empty($_POST['billing_vat']))
                    update_user_meta($user_id, 'billing_vat', sanitize_text_field($_POST['billing_vat'])); //sanitize
            }
            if ($abilitazione_cf == 1) {
                if (!empty($_POST['billing_cf']))
                    update_user_meta($user_id, 'billing_cf', sanitize_text_field($_POST['billing_cf']));
            }
            if ($abilitazione_nin == 1) {
                if (!empty($_POST['billing_nin']))
                    update_user_meta($user_id, 'billing_nin', sanitize_text_field($_POST['billing_nin']));
            }
            if ($abilitazione_pec == 1) {
                if (!empty($_POST['billing_pec']))
                    update_user_meta($user_id, 'billing_pec', sanitize_text_field($_POST['billing_pec']));
            }
            if (!empty($_POST['billing_fatt']))
                $dato_fatt = 'Si';
            update_user_meta($user_id, 'billing_fatt', sanitize_text_field($dato_fatt));
        }
    add_action( 'woocommerce_customer_save_address', 'wp_partita_iva_store_customer_billing_fields_in_address' );


        /*    Salvo le modifiche sui campi custom effettuate nel checkout dell'utente
        */
        function wp_partita_iva_store_customer_billing_fields_in_checkout( $user_id )
        {
            $settings = get_option('wp_partita_iva_settings', array());

            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];
            if ($abilitazione_pi == 1) {
                if (!empty($_POST['billing_vat']))
                    update_user_meta($user_id, 'billing_vat', sanitize_text_field($_POST['billing_vat']));
            }
            if ($abilitazione_cf == 1) {
                if (!empty($_POST['billing_cf']))
                    update_user_meta($user_id, 'billing_cf', sanitize_text_field($_POST['billing_cf']));
            }
            if ($abilitazione_nin == 1) {
                if (!empty($_POST['billing_nin']))
                    update_user_meta($user_id, 'billing_nin', sanitize_text_field($_POST['billing_nin']));
            }
            if ($abilitazione_pec == 1) {
                if (!empty($_POST['billing_pec']))
                    update_user_meta($user_id, 'billing_pec', sanitize_text_field($_POST['billing_pec']));
            }
            if (!empty($_POST['billing_fatt']))
                $dato_fatt = 'Si';
            update_user_meta($user_id, 'billing_fatt', sanitize_text_field($dato_fatt));
        }

    add_action( 'woocommerce_checkout_update_user_meta', 'wp_partita_iva_store_customer_billing_fields_in_checkout' );


        /*    Valido le modifiche sui campi custom effettuate nel checkout dell'utente.
        *     Almeno uno dei due campi deve essere compilato.
        */
        function wp_partita_iva_validate_customer_billing_fields_in_checkout()
        {
            $billing_vat = sanitize_text_field(trim($_POST['billing_vat']));
            $billing_cf = sanitize_text_field(trim($_POST['billing_cf']));
            $billing_nin = sanitize_text_field(trim($_POST['billing_nin']));
            $billing_pec = sanitize_text_field(trim($_POST['billing_pec']));

            if (empty($billing_vat) && empty($billing_cf))
                wc_add_notice(__('You must insert a Codice Fiscale or Vat Code.'), 'error');
            if (empty($billing_nin) && empty($billing_pec))
                wc_add_notice(__('Devi inserire un Codice Cliente o una casella di Posta Elettronica Certificata.'), 'error');
        }

    // Reinserting custom billing state and post code checkout fields
    add_filter('woocommerce_default_address_fields', 'wp_partita_iva_override_default_address_fields');
    function wp_partita_iva_override_default_address_fields($address_fields)
    {
        // @ for state
        $address_fields['billing_state']['type'] = 'text';
        $address_fields['billing_state']['class'] = array('form-row-wide');
        $address_fields['billing_state']['required'] = false;
        $address_fields['billing_state']['label'] = __('State', 'woocommerce');
        $address_fields['billing_state']['placeholder'] = __('State', 'woocommerce');

        return $address_fields;
    }
        /*    Aggiungo campi custom nel riepilogo ordine
        */
        function wp_partita_iva_add_customer_billing_fields_in_admin_order_meta( $order )
        {
            $settings = get_option('wp_partita_iva_settings', array());
            $abilitazione_cf = $settings['basic']['field-cf'];
            $abilitazione_pi = $settings['basic']['field-pi'];
            $abilitazione_nin = $settings['basic']['field-nin'];
            $abilitazione_pec = $settings['basic']['field-pec'];
            $orderid = $order->get_id();
            if ($abilitazione_pi == 1) {
                $billing_vat = get_user_meta($order->get_user_id(), 'billing_vat', true);
                $order_billing_vat = get_post_meta($orderid, '_billing_vat', true);
                $client_vat_label = '<p><strong>' . __('Partita IVA', 'woocommerce') . ' </strong><br>';
                $client_vat_label .= ($billing_vat) ? '(From Billing Address)' . $billing_vat : ($order_billing_vat) ? '(From Order Note)' . $order_billing_vat : '0';
                echo $client_vat_label . '</p>';
            }
            if ($abilitazione_cf == 1) {
                $billing_cf = get_user_meta($order->get_user_id(), 'billing_cf', true);
                $order_billing_cf = get_post_meta($orderid, '_billing_cf', true);
                $billing_cf_label = '<p><strong>' . __('Codice Fiscale', 'woocommerce') . '</strong><br>';
                $billing_cf_label .= ($billing_cf) ? $billing_cf : ($order_billing_cf) ? $order_billing_cf : 'no';
                echo $billing_cf_label . '</p>';
            }
            if ($abilitazione_nin == 1) {
                $billing_nin = get_user_meta($order->get_user_id(), 'billing_nin', true);
                $order_billing_nin = get_post_meta($orderid, '_billing_nin', true);
                $billing_nin_label = '<p><strong>' . __('Codice Univoco', 'woocommerce') . '</strong><br>';
                $billing_nin_label .= ($billing_nin) ? $billing_nin : ($order_billing_nin) ? $order_billing_nin : 'no';
                echo $billing_nin_label . '</p>';
            }
            if ($abilitazione_pec == 1) {
                $billing_pec = get_user_meta($order->get_user_id(), 'billing_pec', true);
                $order_billing_pec = get_post_meta($orderid, '_billing_pec', true);
                $billing_pec_label = '<p><strong>' . __('Indirizzo PEC', 'woocommerce') . '</strong><br>';
                $billing_pec_label .= ($billing_pec) ? $billing_pec : ($order_billing_pec) ? $order_billing_pec : 'no';
                echo $billing_pec_label . '</p>';
            }
            $billing_fatt = get_user_meta($order->get_user_id(), 'billing_fatt', true);
            $order_billing_fatt = get_post_meta($orderid, '_billing_fatt', true);
            $billing_fatt_label = '<p><strong>' . __('Necessaria Fattura', 'woocommerce') . '</strong><br>';
            $billing_fatt_label .= ($billing_fatt) ? $billing_fatt : ($order_billing_fatt) ? $order_billing_fatt : 'no';
            echo $billing_fatt_label . '</p>';
    }
    add_action( 'woocommerce_admin_order_data_after_billing_address', 'wp_partita_iva_add_customer_billing_fields_in_admin_order_meta', 10, 1 );

    /*    Salvataggio campi fattura elettronica se utente non chiede di registrarsi (ospite)
    */
        function wp_partita_iva_before_checkout_create_order( $order )
        {
            $billing_pec = sanitize_text_field(trim($_POST['billing_pec']));
            $billing_nin = sanitize_text_field(trim($_POST['billing_nin']));

            update_post_meta($order->id, '_billing_nin', sanitize_text_field($billing_nin));
            update_post_meta( $order->id, '_billing_pec', sanitize_text_field( $billing_pec ) );
        }

    add_action('woocommerce_checkout_update_order_meta', 'wp_partita_iva_before_checkout_create_order');

    add_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');
    // Utility function sending the email notification
    function send_seller_email($order)
    {
        $data = array();
        $orderid = $order->get_id();
        // Loop through each order item
        foreach ($order->get_items() as $item_id => $item) {
            if (get_post_meta($order->get_id(), '_sent_to_seller_' . $item_id, true))
                continue; // Go to next loop iteration

            $product_id = $item->get_product_id();

            $seller_email = get_bloginfo('admin_email');

            // Set the data in an array (avoiding seller email repetitions)
            $data[$seller_email][] = array(
                'excerpt' => get_the_excerpt($product_id),
                'title' => get_the_title($product_id),
                'link' => get_permalink($product_id),
            );
            // Update order to avoid notification repetitions
            update_post_meta($order->get_id(), '_sent_to_seller_' . $item_id, true);
        }

        if (count($data) == 0) return;
        $nome = get_post_meta($orderid, '_billing_first_name', true);
        $cognome = get_post_meta($orderid, '_billing_last_name', true);
        $company = get_post_meta($orderid, '_billing_company', true);
        $email = get_post_meta($orderid, '_billing_email', true);
        $tel = get_post_meta($orderid, '_billing_phone', true);
        $indirizzo1 = get_post_meta($orderid, '_billing_address_1', true);
        $indirizzo2 = get_post_meta($orderid, '_billing_address_2', true);
        $city = get_post_meta($orderid, '_billing_city', true);
        $state = get_post_meta($orderid, '_billing_state', true);
        $cap = get_post_meta($orderid, '_billing_postcode', true);
        $vat = get_post_meta($orderid, '_billing_vat', true);
        $cf = get_post_meta($orderid, '_billing_cf', true);
        $nin = get_post_meta($orderid, '_billing_nin', true);
        $pec = get_post_meta($orderid, '_billing_pec', true);
        $richiesta = get_post_meta($orderid, '_billing_fatt', true);

        $subtotale = $order->get_subtotal();
        $tasse = $order->get_total_tax();
        $totale = $order->get_total();

        // Loop through custom data array to send mails to sellers
        foreach ($data as $email_key => $values) {
            $to = $email_key;
            $subject_arr = array();
            $message = '';

            foreach ($values as $value) {
                $subject_arr[] = $value['title'];
                $message .= 'Buongiorno,<br>';
                $message .= 'è stato richiesto di emettere una nuova fattura elettronica. Di seguito i dettagli per la fattura elettronica:<br>';
                $message .= 'ID ORDINE: ' . $orderid . ' <br>';
                $message .= 'NOME: ' . $nome . ' <br>';
                $message .= 'COGNOME:' . $cognome . ' <br>';
                $message .= 'AZIENDA: ' . $company . ' <br>';
                $message .= 'EMAIL: ' . $email . ' <br>';
                $message .= 'TELEFONO: ' . $tel . ' <br>';
                $message .= 'INDIRIZZO: ' . $indirizzo1 . ' ' . $indirizzo2 . ' ' . $city . ' ' . $state . ' ' . $cap . ' ' . ' <br>';
                $message .= 'PARTITA IVA: ' . $vat . ' <br>';
                $message .= 'CODICE FISCALE: ' . $cf . ' <br>';
                $message .= 'CODICE CLIENTE PER FATTURAZIONE ELETTRONICA: ' . $nin . ' <br>';
                $message .= 'INDIRIZZO PEC PER FATTURAZIONE ELETTRONICA: ' . $pec . ' <br>';
                $message .= 'IMPONIBILE: ' . $subtotale . ' <br>';
                $message .= 'IVA: ' . $tasse . ' <br>';
                $message .= 'TOTALE: ' . $totale . ' <br>';
                $message .= 'RICHIESTA FATTURA: ' . $richiesta . ' <br>';

            }
            $subject = 'Richiesta di invio fattura elettronica: ';

            // Send email to seller
            wp_mail($to, $subject, $message);

        }
    }

    add_action('woocommerce_new_order', 'new_order_seller_notification', 10, 1);
    function new_order_seller_notification($order_id)
    {
        $order = wc_get_order($order_id);
        $richiesta = get_post_meta($order_id, '_billing_fatt', true);

        if (!($order->has_status('completed')))
            return; // Exit
        if ($richiesta == 'Si') {
            send_seller_email($order);
        }
    }

    add_action('woocommerce_order_status_changed', 'order_status_seller_notification', 20, 4);
    function order_status_seller_notification($order_id, $status_from, $status_to, $order)
    {
        $richiesta = get_post_meta($order_id, '_billing_fatt', true);

        if (!($status_to == 'completed'))
            return; // Exit

        if ($richiesta == 'Si') {
            send_seller_email($order);
        }

    }

    function wpdocs_set_html_mail_content_type()
    {
        return 'text/html';
    }
}