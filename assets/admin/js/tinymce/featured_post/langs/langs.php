<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( '_WP_Editors' ) )
    require( ABSPATH . WPINC . '/class-wp-editor.php' );

function shortcode_cffp_translations() {
    $strings = array(
    	// editor menu button title
    	'button_title'		=> __('Insert new featured post', 'cfp'),
        // edit shortcode window
    	'add_new_window_title' => __('Insert new featured post', 'cfp'),
    	'window_title' 		=> __('Edit featured post properties', 'cfp'),
    	
        'label_category'		=> __('Category ID', 'cfp'),
    	'label_taxonomy' 		=> __('Taxonomy name (optional)', 'cfp'),
    	'label_post_type' 	=> __('Post type (optional)', 'cfp'),
    	'label_post_id' 	=> __('Post ID (optional)', 'cfp'),
    	'label_template' 		=> __('Template', 'cfp'),
    	
    	// add shortcode window
    	'close_win'			=> __('Close', 'cfp') 
    );
    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.cffp", ' . json_encode( $strings ) . ");\n";
    
    return $translated;
}
$strings = shortcode_cffp_translations();