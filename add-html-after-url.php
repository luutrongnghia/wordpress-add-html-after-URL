<?php

/*
  Plugin Name: add .html after URL
  Plugin URI: http://haita.media
  Description: Plugin is used for rewrite URL by default of page name or post name with new URL add .html at the end of URLs
  Author: Luu Trong Nghia
  Version: 1.0
  Author URI: http://haita.media
 */

/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

 */

define('HAiTA_HTML_VERSION', '1.0');
define('HAiTA_HTML_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HAiTA_HTML_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HAiTA_HTML_DELETE_LIMIT', 100000);

add_action('init', 'haita_rewrite_permalink_structure', -1);
register_activation_hook(__FILE__, 'haita_rewriteURL_activate');
register_deactivation_hook(__FILE__, 'haita_rewriteURL_deactivate');

global $permalink_structure; 

function haita_rewrite_permalink_structure() {
    global $wp_rewrite;
    $permalink_structure = get_option( 'permalink_structure' );
    if(!strpos($permalink_structure, '.html')){
         $permalink_structure = '/%postname%.html';
         $wp_rewrite->set_permalink_structure($permalink_structure); 
    }
    if (!strpos($wp_rewrite->get_page_permastruct(), '.html')) {
        $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
    }
}

add_filter('user_trailingslashit', 'haita_rewriteURL_remove_slash', 66, 2);

function haita_rewriteURL_remove_slash($string, $type) {
    global $wp_rewrite;
    if ($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes == true && $type == 'page') {
        return untrailingslashit($string);
    } else {
        return $string;
    }
}

function haita_rewriteURL_activate() {
    global $wp_rewrite;
    haita_rewrite_permalink_structure();
    $wp_rewrite->flush_rules();
}

function haita_rewriteURL_deactivate() {
    global $wp_rewrite;
    $permalink_structure = str_replace(".html", "/", get_option( 'permalink_structure' ));
    $wp_rewrite->set_permalink_structure($permalink_structure);
    $wp_rewrite->page_structure = str_replace(".html", "", $wp_rewrite->page_structure);
    $wp_rewrite->flush_rules();
}
?>