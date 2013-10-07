<?php

if ( defined( 'WPMLM_LOAD_DEPRECATED' ) )
	require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-deprecated.php' );

require_once( WPMLM_FILE_PATH . '/wpmlm-core/common.class.php' );
// Start including the rest of the plugin here
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/theme.functions.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/ajax.functions.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/misc.functions.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/display.functions.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/form-display.functions.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/meta.functions.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-includes/network-function.php' );

// Editor
require_once( WPMLM_CORE_JS_PATH . '/tinymce3/tinymce.php' );

// Themes


require_once( WPMLM_FILE_PATH . '/wpmlm-admin/admin-form-functions.php' );


// Gregs ASH Shipping

// Admin
if ( is_admin() )
	include_once( WPMLM_FILE_PATH . '/wpmlm-admin/admin.php' );

require_once( WPMLM_FILE_PATH . '/wpmlm-includes/cron.php' );

?>