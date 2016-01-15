<?php
// No direct include
if( !defined('ABSPATH') ){
	die();
}

/**
 * Returns all registered shortcodes
 * @return array
 */
function cfp_get_shortcodes(){
	return CFP_Register_Shortcodes::get_shortcodes();
}

/**
 * Returns all registered templates
 * @return array
 */
function cfp_get_templates(){
    return CFP_Register_Shortcodes::get_templates();
}

/**
 * Displays a drop-down select box populated with all registered templates
 * @param array $args - @see cfp_dropdown() for more details 
 */
function cfp_templates_dropdown( $args = array() ){
    
    $templates = cfp_get_templates();
    $options = array();
    
    foreach( $templates as $key => $template ){
        $options[ $key ] = isset( $template['name'] ) ? $template['name'] : $key;
    }
    
    $args['options'] = $options;
    return cfp_dropdown( $args );
}

/**
 * Displays a drop-down select box populated with all publicly registered taxonomies
 * @param array $args - @see cfp_dropdown() for more details 
 */
function cfp_taxonomies_dropdown( $args = array() ){
    
    $options = cfp_get_taxonomies();
    $args['options'] = $options;
    return cfp_dropdown( $args );
}

/**
 * Returns an array of type tax_ID => taxonomy name for all publicly
 * registered taxonomies
 * @return multitype:NULL
 */
function cfp_get_taxonomies(){
    $taxonomies = get_taxonomies(array(
        'public' => true
    ), 'objects');
    
    $options = array();
    foreach( $taxonomies as $tax => $data ){
        $options[ $tax ] = $data->labels->singular_name;
    }
    return $options;
}

/**
 * Displays a drop-down select box populated with all publicly registered post types
 * @param array $args - @see cfp_dropdown() for more details
 */
function cfp_post_types_dropdown( $args = array() ){
    $options = cfp_get_post_types();
    $args['options'] = $options;
    return cfp_dropdown( $args );
}

/**
 * Returns an array structured as post_type => post type name of all public
 * post types registered
 * 
 * @return multitype:NULL
 */
function cfp_get_post_types(){
    // post types list
    $post_types = get_post_types(array(
        'public' => true
    ), 'objects');
    
    $options = array();
    foreach( $post_types as $type => $data ){
        $options[ $type ] = $data->labels->singular_name;
    }
    return $options;
}

/**
 * Return template absolute path
 * @param string $part
 */
function cfp_template_abs_path( $part ){
    return CFP_PATH . '/views/' . $part . '.template.php';    
}

/**
 * 
 * @param unknown $var
 */
function cfp_value_to_txt( $var ){
    if( is_bool( $var )){
        $var = $var ? 'true' : 'false';        
    }
    
    echo $var;
}

/**
 * Display select box
 * @param array $args - see $defaults in function
 * @param bool $echo
 */
function cfp_dropdown( $args = array() ){

    $defaults = array(
        'options' 	=> array(),
        'name'		=> false,
        'id'		=> false,
        'class'		=> '',
        'selected'	=> false,
        'use_keys'	=> true,
        'hide_if_empty' => true,
        'show_option_none' => __('No options', 'cfp'),
        'select_opt'	=> __('Choose', 'cfp'),
        'select_opt_style' => false,
        'attrs'	=> '',
        'echo' => true
    );

    extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

    if( $hide_if_empty  && !$options && !$show_option_none){
        return;
    }

    if( !$id ){
        $id = $name;
    }

    $output = sprintf( '<select autocomplete="off" name="%1$s" id="%2$s" class="%3$s" %4$s>', esc_attr( $name ), esc_attr( $id ), esc_attr( $class ), $attrs );
    if( !$options && $show_option_none ){
        $output .= '<option value="">' . $show_option_none . '</option>';
    }elseif( $select_opt ){
        $output .= '<option value=""'. ( $select_opt_style ? ' style="' . $select_opt_style . '"' : '' ) .'>' . $select_opt . '</option>';
    }

    foreach( $options as $val => $text ){
        $opt = '<option value="%1$s"%2$s>%3$s</option>';
        $value = $use_keys ? $val : $text;
        $c = $use_keys ? $val == $selected : $text == $selected;
        $checked = $c ? ' selected="selected"' : '';
        $output .= sprintf($opt, $value, $checked, $text);
    }

    $output .= '</select>';

    if( $echo ){
        echo $output;
    }

    return $output;
}