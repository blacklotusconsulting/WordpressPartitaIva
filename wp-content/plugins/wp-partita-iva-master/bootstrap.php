<?php
/*
Plugin Name: Partita Iva e Fattura Elettronica per Woocommerce
Plugin URI:  https://github.com/hoz1982/wp-partita-iva
Description: Partita Iva e Fattura Elettronica per Woocommerce Aggiunge il supporto per l'inserimento nel form di pagamento di Woocommerce dei campi Partita IVA, Codice Fiscale, Codice Cliente e indirizzo PEC, necessari alla fatturazione elettronica in Italia. Inserisce inoltre una checkbox nel form di pagamento in cui l'utente può indicare la necessità di una fattura elettronica per l'acquisto, che può essere gestita esternamente. All'emissione dell'ordine una mail di avviso verrà inviata all'indirizzo dell'amministratore del sito, contenente tutte le informazioni necessarie all'emissione.

Partita Iva e Fattura Elettronica per Woocommerce adds to the Woocommerce standard checkout form some custom fields(VAT Number, Fiscal Code, NIN Code and PEC email address) required for the italian market.

Version:     1.1
Author:      Alessandro Romani
Author URI:  https://www.blacklotus.eu
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Partita Iva e Fattura Elettronica per Woocommerce  is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.

Partita Iva e Fattura Elettronica per Woocommerce  is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Partita Iva e Fattura Elettronica per Woocommerce . If not, see https://www.gnu.org/licenses/gpl-2.0.html.

Tags: fattura-elettronica,fattura,elettronica,woocommerce,woocommerce-fattura-elettronica,codice-fiscale,pec,iva,codice-univoco,codice-cliente
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

define('wp_partita_iva_NAME', 'Partita Iva e Fattura Elettronica per Woocommerce');
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