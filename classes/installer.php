<?php
/**
 * Created by Alessandro Romani.
 * Date: 09/03/2020
 * Time: 14:18
 * This installer class will install the required DB Table for the WP Partita Iva Plugin and default values.
 */

global $wpdb;
$table_name = $wpdb->prefix . "wp-partita-iva";
$db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {

    $sql = "CREATE TABLE $table_name (
            ID mediumint(9) NOT NULL AUTO_INCREMENT,
            `campo` text NOT NULL,
            `obbligatorio` text NOT NULL,
            `mostrare` int(9) NOT NULL,
            PRIMARY KEY  (ID)
    )    $charset_collate;
    INSERT INTO $table_name (campo, obbligatorio, mostrare)
    VALUES ('cf', '0', '1'),('pi', '0', '1'),('nin', '0', '1'),('pec', '0', '1');
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('my_db_version', $db_version);
}