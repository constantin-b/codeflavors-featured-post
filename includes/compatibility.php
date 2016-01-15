<?php 
/**
 * Initialize Visual Composer component for front-end editing
 */
add_action('admin_init', 'cfp_initialize_visual_composer');
function cfp_initialize_visual_composer(){
    
    if( !function_exists( 'vc_map' ) ){
        return;
    }
    
    $templates = cfp_get_templates();
    $template_options = array();
    foreach( $templates as $key => $template ){
        $template_options[ $key ] = isset( $template['name'] ) ? $template['name'] : $key;
    }
    
    vc_map( array(
        'name'     	=> __( 'CodeFlavors Featured Post', 'cfp' ),
        'description' => __( 'Feature any post type.', 'cfp' ),
        'base'	  	=> "codeflavors_featured_post",
        'class'    	=> '',
        'icon'     	=> cfp_get_uri( 'assets/admin/images/featured.png' ),
        'category'	=> __( 'Content', 'cfp' ),
        'params'    => array(
            array(
                'type'       	=> 'dropdown',
        		//'holder'      	=> 'div',
                'admin_label' => true,
                'class'       	=> '',
        		'heading'     	=> __( 'Post type', "cfp" ),
        		'param_name'	=> 'post_type',
        		'value'       	=> array_flip( cfp_get_post_types() ),
        		'description'	=> '',
            ),
            array(
                'type' => 'dropdown',
                //'holder' => 'div',
                'class' => '',
                'heading' => __( 'Taxonomy name', 'cfp' ),
                'param_name' => 'taxonomy',
                'value' => array_flip( cfp_get_taxonomies() ),
                'description' => ''
            ),
            array(
                'type' => 'textfield',
                //'holder' => 'div',
                'class' => '',
                'heading' => __( 'Category/Term', 'cfp' ),
                'param_name' => 'category',
                'description' => __( 'Enter category/term name or ID', 'cfp' ),
            ),
            array(
                'type' => 'textfield',
                //'holder' => 'div',
                'class' => '',
                'heading' => __( 'Number of posts', 'cfp' ),
                'param_name' => 'post_num',
                'value' => '1',
                'description' => __( 'Number of posts to retrieve', 'cfp' )
            ),
            array(
                'type' => 'textfield',
                //'holder' => 'div',
                'class' => '',
                'heading' => __( 'Post offset', 'cfp' ),
                'param_name' => 'offset',
                'description' => __( 'Skip posts (ie. to retrieve starting with the second post, set the offset to 1)', 'cfp' ),
                'value' => '0'
            ),
             array(
                'type' => 'textfield',
                //'holder' => 'div',
                'class' => '',
                'heading' => __( 'Post ID', 'cfp' ),
                'param_name' => 'post_id',
                'description' => __( 'When set, will override all other parameters and return the post having this ID.', 'cfp' )
            ),
            array(
                'type' => 'dropdown',
                //'holder' => 'div',
                'class' => '',
                'heading' => __( 'Template', 'cfp' ),
                'param_name' => 'template',
                'value' => array_flip( $template_options ),
                'description' => __( 'Select one of the available templates to display the featured post.', 'cfp' ),
            ),
        )
    ));    
}