<?php
// No direct include
if( !defined('ABSPATH') ){
	die();
}

/**
 * Admin class. Implements all plugin administration
 *
 * @since 1.0
 * @package Video WP plugin
 */
class CFP_Admin{

	/**
	 * @var instance
	 **/
	private static $instance = null;
	
	static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new CFP_Admin;
		}
		return self::$instance;
	}
	
	/**
	 * Constructor, instantiates hooks and filters
	 */
	private function __construct(){
		
		// admin menu
		add_action( 'admin_menu' , array( $this, 'admin_menu' )  );
		
		// tinymce
		//add_action('admin_head', array( $this, 'tinymce' ) );
		//add_filter('mce_external_languages', array( $this, 'tinymce_languages' ) );
	}
	
	/**
	 * Add plugin admin menu
	 * @return void
	 */
	public function admin_menu() {
        add_theme_page(
            __( 'CodeFlavors Featured Post', 'cfp' ), 
            __( 'CodeFlavors Featured Post', 'cfp' ), 
            'edit_posts', 
            'cfp-options',
            array( $this, 'plugin_options' ) );  
	}
	
	/**
	 * Output plugin page
	 */
	public function plugin_options(){
	    
	    require_once cfp_template_abs_path( 'plugin_options' ) ;
	    
	}
	
	/**
	 * Adds tinyce plugins to editor
	 */
	public function tinymce(){
	    // Don't bother doing this stuff if the current user lacks permissions
	    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
	        return;
	     
	    // Don't load unless is post editing (includes post, page and any custom posts set)
	    $screen = get_current_screen();
	    if( 'post' != $screen->base ){
	        return;
	    }
	
	    // Add only in Rich Editor mode
	    if ( version_compare( get_bloginfo( 'version' ) , '4', '>=' ) && get_user_option('rich_editing') == 'true') {
	        add_filter('mce_external_plugins', array( $this, 'tinymce_plugins' ) );
	        add_filter('mce_buttons', array( $this, 'tinyce_buttons' ) );
	        add_filter('mce_css', array( $this, 'tinymce_css' ) );
	    }
	}
	
	/**
	 * Filter mce_buttons callback.
	 */
	public function tinyce_buttons( $mce_buttons ){
	    array_push( $mce_buttons, 'separator', 'cf_featured_post' );
	    return $mce_buttons;
	}
	
	/**
	 * Filter mce_external_plugins callback function.
	 */
	public function tinymce_plugins( $plugin_array ) {
	    $plugin_array['cf_featured_post'] = cfp_get_uri ( 'assets/admin/js/tinymce/featured_post/plugin.js' );
	    return $plugin_array;
	}
	
	/**
	 * Filter mce_css callback function.
	 */
	public function tinymce_css( $css ){
	    $css .= ',' . cfp_get_uri( 'assets/admin/js/tinymce/featured_post/style.css' );
	    return $css;
	}
	
	/**
	 * Add tinyMce plugin translations
	 */
	public function tinymce_languages( $locales ){
	    $locales['cf_featured_post'] = cfp_path( 'assets/admin/js/tinymce/featured_post/langs/langs.php' );
	    return $locales;
	}
}
CFP_Admin::init();