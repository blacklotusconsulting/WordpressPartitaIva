<?php
/*
===  Partita Iva per Fattura Elettronica  ===
Contributors: blacklotusconsulting
Tags: fattura-elettronica,fattura,elettronica,woocommerce,woocommerce-fattura-elettronica,codice-fiscale,pec,iva,codice-univoco,codice-cliente,piva,fatturazione-elettronica,fattura-woocommerce,woocommerce-vat,vat-number,vat-number-woocommerce
Requires at least: 5.1
Tested up to: 6.0.2
Requires PHP: 7.1
Stable tag: 1.2.2
Version:     1.2.2
Author:      Alessandro Romani
Author URI:  https://www.blacklotus.eu
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin Name: Partita Iva per Fattura Elettronica
Plugin URI:  https://github.com/blacklotusconsulting/WordpressPartitaIva
Donate Link: https://www.paypal.com/donate/?business=PZXF7XXABDC7C&no_recurring=0&currency_code=EUR

Description:

Partita Iva per Fattura Elettronica adds to the Woocommerce standard checkout form some custom fields(VAT Number, Fiscal Code, NIN Code and PEC email address) required for the italian market.
It is possible to choose the fields that will be displayed in the checkout form and set as mandatory field for each field.
Data are stored as Woocommerce Order Meta.
Requires Woocommerce

Partita Iva per Fattura Elettronica Aggiunge il supporto per l'inserimento nel form di pagamento di Woocommerce dei campi Partita IVA, Codice Fiscale, Codice Cliente e indirizzo PEC, necessari alla fatturazione elettronica in Italia.

Con questa plugin in versione gratuita vengono aggiunte al form di pagamento standard di Woocommerce le seguenti funzionalità:
1) Inserimento del campo Codice Fiscale nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
2) Inserimento del campo Partita Iva nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
3) Inserimento del campo Indirizzo PEC nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
4) Inserimento del campo Codice Univoco Iva nel form di pagamento di Woocommerce con salvataggio del dato nei metadati dell'ordine
5) Inserimento del campo Checkbox per la richiesta di fattura elettronica. Se valorizzato in fase di acquisto verrà inviata una mail all'indirizzo dell'amministratore del sito con i dettagli per la fatturazione elettronica.
6) Pannello di controllo per abilitare/disabilitare la visibilità e l'obbligatorietà dei campi nel form di pagamento.

Per il corretto funzionamento è necessaria l'installazione di Woocommerce.

== Frequently Asked Questions ==
Does this plugin require Woocommerce?
Yes, it requires Woocommerce.

Is this plugin FREE?
Yes, it is free. If you want you can buy me a chocolate cake donating at this link https://www.paypal.com/donate/?business=PZXF7XXABDC7C&no_recurring=0&currency_code=EUR

Does this plugin create a PDF of the invoice?
Nope. It just save VAT Number, Fiscal Code, NIN Code and PEC email address as order meta data.

== Changelog ==
1.2.1 Added more information on the readme file, Plugin Banner and Plugin Icon.
1.2 Fix incorrect Stable Tag. Variables and options escaped when echo'd
1.1 Fixed code to Sanitize Escape and Validate data.
1.0 First release.

== Upgrade Notice ==
No upgrade notice so far

== Screenshots ==
1.Form Custom Fields in Order Page
2.Settings to enable/disable and make custom Fields mandatory in Wordpress settings page
3. Admin order page with saved Custom Fields as order meta
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

define('wp_partita_iva_NAME', 'Partita Iva per Fattura Elettronica');
define( 'wp_partita_iva_REQUIRED_PHP_VERSION', '5.3' );                          // because of get_called_class()
define( 'wp_partita_iva_REQUIRED_WP_VERSION',  '3.1' );                          // because of esc_textarea()

/**tem requirements are met, false if not
 */
function wp_partita_iva_requirements_met() {
	global $wp_version;
	//require_once( ABSPATH . '/wp-admin/includes/plugin.php' );		// to get is_plugin_active() early

	if ( version_compare( PHP_VERSION, wp_partita_iva_REQUIRED_PHP_VERSION, '<' ) ) {
		return false;
	}

	if ( version_compare( $wp_version, wp_partita_iva_REQUIRED_WP_VERSION, '<' ) ) {
		return false;
	}


	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 */
function wp_partita_iva_requirements_error() {
	global $wp_version;

	require_once( dirname( __FILE__ ) . '/views/requirements-error.php' );
}

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the plugin requirements are met. Otherwise older PHP installations could crash when trying to parse it.
 */
if ( wp_partita_iva_requirements_met() ) {
	require_once( __DIR__ . '/classes/wp_partita_iva-module.php' );
	require_once( __DIR__ . '/classes/wordpress_partita_iva.php' );
	require_once( __DIR__ . '/classes/wp_partita_iva-settings.php' );
	require_once( __DIR__ . '/classes/wp_partita_iva-cron.php' );
	require_once( __DIR__ . '/classes/wp_partita_iva-instance-class.php' );

    if (class_exists('WordPress_Partita_IVA')) {
		$GLOBALS['wp_partita_iva'] = WordPress_Partita_IVA::get_instance();
		register_activation_hook(   __FILE__, array( $GLOBALS['wp_partita_iva'], 'activate' ) );
		register_deactivation_hook( __FILE__, array( $GLOBALS['wp_partita_iva'], 'deactivate' ) );
	}
} else {
	add_action( 'admin_notices', 'wp_partita_iva_requirements_error' );
}