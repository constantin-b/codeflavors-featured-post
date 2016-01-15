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
    	
        'label_category'		=> __('Category/Term (optional)', 'cfp'),
    	'label_taxonomy' 		=> __('Taxonomy name (optional)', 'cfp'),
    	'label_post_type' 	    => __('Post type', 'cfp'),
    	'label_post_id' 	    => __('Post ID (if set, will override all other options)', 'cfp'),
        'label_post_num'        => __('Number of posts', 'cfp'),
        'label_post_offset'     => __('Post offset', 'cfp'),
    	'label_template' 		=> __('Template', 'cfp'),
    	
    	// add shortcode window
    	'close_win'			=> __('Close', 'cfp') 
    );
    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.cffp", ' . json_encode( $strings ) . ");\n";
    
    // templates option
    $templates = cfp_get_templates();
    $options = array();
    foreach( $templates as $key => $template ){
        $options[] = array(
            'value' => $key,
            'text'  => isset( $template['name'] ) ? $template['name'] : $key
        );
    }
    $translated .= 'var cffp_templates = ' . json_encode( $options ) . ";\n";
    
    // taxonomies list
    $taxonomies = get_taxonomies(array(
        'public' => true
    ), 'objects');
    
    $options = array( array( 'value' => '', 'text' => __( 'No taxonomy', 'cfp' ) ) );
    foreach( $taxonomies as $tax => $data ){
        $options[] = array(
            'value' =>  $tax,
            'text' => $data->labels->singular_name
        );
    }
    $translated .= 'var cffp_taxonomies = ' . json_encode( $options ) . ";\n";
    
    // post types list
    $post_types = get_post_types(array(
        'public' => true
    ), 'objects');
    
    $options = array();
    foreach( $post_types as $type => $data ){
        $options[] = array(
            'value' =>  $type,
            'text' => $data->labels->singular_name
        );
    }
    $translated .= 'var cffp_post_types = ' . json_encode( $options ) . ";\n";
    
    return $translated;
}
$strings = shortcode_cffp_translations();