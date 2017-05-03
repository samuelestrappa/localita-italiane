<?php 
/*
Plugin Name: Località italiane
Description: "Località italiane" mette a disposizione le regioni, le province e i comuni d'Italia
Version: 1.0
Author: Progetto Resocosp
Author URI: http://samuelestrappa.wordpress.com

*/

define( 'LOCALITAITALIANE_PLUGIN_DIR', plugin_dir_path(__FILE__));

register_activation_hook( __FILE__, array( 'LocalitaItaliane', 'activation'));
register_deactivation_hook( __FILE__, array( 'LocalitaItaliane', 'deactivation'));

require_once( LOCALITAITALIANE_PLUGIN_DIR . 'localita-italiane.class.php' );

new LocalitaItaliane();
?>