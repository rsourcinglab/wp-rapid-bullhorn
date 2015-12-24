<?php
/*
   Plugin Name: WP Rapid Bullhorn
   Plugin URI: http://wordpress.org/extend/plugins/wp-rapid-bullhorn/
   Version: 0.1
   Author: <a href="http://www.rsourcinglab.com/" target="_blank">Rapid Sourcing Lab</a>
   Description: Bullhorn Integration for WP Job Manager
   Text Domain: wp-rapid-bullhorn
   License: GPLv3
  */


$WpRapidBullhorn_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function WpRapidBullhorn_noticePhpVersionWrong() {
    global $WpRapidBullhorn_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "WP Rapid Bullhorn" requires a newer version of PHP to be running.',  'wp-rapid-bullhorn').
            '<br/>' . __('Minimal version of PHP required: ', 'wp-rapid-bullhorn') . '<strong>' . $WpRapidBullhorn_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'wp-rapid-bullhorn') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function WpRapidBullhorn_PhpVersionCheck() {
    global $WpRapidBullhorn_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $WpRapidBullhorn_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'WpRapidBullhorn_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function WpRapidBullhorn_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('wp-rapid-bullhorn', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','WpRapidBullhorn_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (WpRapidBullhorn_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('wp-rapid-bullhorn_init.php');
    WpRapidBullhorn_init(__FILE__);
}
