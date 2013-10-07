<?php

/**
 * @title TinyMCE V3 Button Integration (for Wp2.5)
 * @author Allen Han
 */

function wpmlm_addbuttons() {

	// Don't bother doing this stuff if the current user lacks permissions
// 	if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
// 	
// 	// Check for NextGEN capability
// 	if ( !current_user_can('NextGEN Use TinyMCE') ) return;
	 
	// Add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true') {
	 
	// add the button for wp25 in a new way
		add_filter("mce_external_plugins", "add_wpmlm_tinymce_plugin", 5);
		add_filter('mce_buttons', 'register_wpmlm_button', 5);
	}
}

// used to insert button in wordpress 2.5x editor
function register_wpmlm_button($buttons) {

	array_push($buttons, "separator", "WPMLM");

	return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_wpmlm_tinymce_plugin($plugin_array) {

	$plugin_array['WPMLM'] =                        WPMLM_CORE_JS_URL . '/tinymce3/editor_plugin.js';
	$plugin_array['productspage_image'] =          WPMLM_CORE_JS_URL . '/tinymce3/editor_plugin.js';
	$plugin_array['transactionresultpage_image'] = WPMLM_CORE_JS_URL . '/tinymce3/editor_plugin.js';
	$plugin_array['checkoutpage_image'] =          WPMLM_CORE_JS_URL . '/tinymce3/editor_plugin.js';
	$plugin_array['userlogpage_image'] =           WPMLM_CORE_JS_URL . '/tinymce3/editor_plugin.js';
	return $plugin_array;
}

function wpmlm_change_tinymce_version($version) {
	return ++$version;
}

// Modify the version when tinyMCE plugins are changed.
add_filter('tiny_mce_version', 'wpmlm_change_tinymce_version');

// init process for button control
add_action('init', 'wpmlm_addbuttons');

?>