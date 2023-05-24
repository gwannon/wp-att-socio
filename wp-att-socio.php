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
