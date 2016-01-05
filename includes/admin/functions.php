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
	return CFP_Shortcodes::get_shortcodes();
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