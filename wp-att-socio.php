<?php

/**
 * Plugin Name: WP Atención al socio Jolaseta
 * Plugin URI:  https://github.com/gwannon/wp-att-socio
 * Description: Plugin de de WordPress de atención al socio que logea contra sistema OMESA.
 * Version:     1.0
 * Author:      Gwannon
 * Author URI:  https://github.com/gwannon/
 * License:     GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-att-socio
 *
 * PHP 7.3
 * WordPress 6.1.1
 */


ini_set("display_errors", 1);

//Cargamos el multi-idioma
function wp_att_socio_plugins_loaded() {
  load_plugin_textdomain('wp-att-socio', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}
add_action('plugins_loaded', 'wp_att_socio_plugins_loaded', 0 );

function get_mail_headers() {
  $headers = array(
    "From: comunicacion@jolaseta.com",
    "Reply-To: comunicacion@jolaseta.com",
    "Content-type: text/html; charset=utf-8"
  );
  return $headers;
}

function wp_att_socio_load_scripts(){
  wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'wp_att_socio_load_scripts');

/* ----------- Includes ------------ */
include_once(plugin_dir_path(__FILE__).'lib/custom_posts.php');
include_once(plugin_dir_path(__FILE__).'lib/application.php');
include_once(plugin_dir_path(__FILE__).'lib/survey.php');
include_once(plugin_dir_path(__FILE__).'lib/shortcodes.php');

/* ---------- Globals ---------------- */
define('WP_ATT_SOCIO_DEFAULT_STATUS', 104);
define('WP_ATT_SOCIO_CLOSED_STATUS', 107);
define('WP_ATT_SOCIO_PAGE_ID', 14836);

/* ----------- Includes ------------ */
/*
include_once(plugin_dir_path(__FILE__).'inc/fields.php');
include_once(plugin_dir_path(__FILE__).'/classes/curl.php');
include_once(plugin_dir_path(__FILE__).'/classes/user.php');
include_once(plugin_dir_path(__FILE__).'/classes/functions.php');/*

/* ---------- Globals ---------------- */
/*define('WPAT_AC_API_URL', get_option("_wpatg_api_url")); 
define('WPATG_AC_API_KEY', get_option("_wpatg_api_key"));
define('WPATG_AC_ENGAGEMENT_AUTOMATION', 105);
define('WPATG_NEWLETTERS_FILTER', get_option("_wpatg_newsletter_filter"));
define('WPATG_MAIN_NEWLETTER_ID', get_option("_wpatg_main_newsletter_id"));
define('WPATG_LAST_UPDATE_FIELD_ID', 39);
define('WPATG_ARCHIVE_MAX_ITEMS', 20);
define('WPATG_COOKIE_TIME', (60 * 60 * 24));
define('WPATG_ARCHIVE_CACHE_FILE', plugin_dir_path(__FILE__).'archive.json');
define('WPATG_ARCHIVE_CACHE_TIME', (60 * 60 * 24));
if(!defined('ICL_LANGUAGE_CODE'))  define('ICL_LANGUAGE_CODE', "es");*/

/* -------------------- Cookies ------------------ */
/*function wpatg_manage_cookie(){
  if(isset($_REQUEST['wpatg_logout']) && $_REQUEST['wpatg_logout'] == 'yes') {
    setcookie("wpatg", "");  //Borramos la cookie
    wp_redirect(get_the_permalink());
  }	else if(isset($_REQUEST['wpatg_hash']) && $_REQUEST['wpatg_hash'] != '' && isset($_REQUEST['wpatg_contact_id']) && $_REQUEST['wpatg_contact_id'] != '') {
    $value = [
      "wpatg_hash" => $_REQUEST['wpatg_hash'],
      "wpatg_contact_id" => $_REQUEST['wpatg_contact_id']
    ];
		setcookie("wpatg", json_encode($value), time() + WPATG_COOKIE_TIME);
    wp_redirect(get_the_permalink().(isset($_REQUEST['wpatg_tab']) &&  $_REQUEST['wpatg_tab'] != '' ? "?wpatg_tab=".$_REQUEST['wpatg_tab'] : ""));
	}
}
add_action( "template_redirect", "wpatg_manage_cookie");*/
