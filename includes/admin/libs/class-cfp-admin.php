<?php
// No direct include
if( !defined('ABSPATH') ){
	die();
}

abstract class CFP_Admin_Helper{
    /**
     * Enqueue admin specific scripts
     *
     * @param string $script
     * @param array $dependency
     * @return string - script handle
     */
    protected function enqueue_script( $script, $dependency = array() ){
        $script = preg_replace( '|([^a-z\-\_\.])|' , '', $script);
        $url 	= cfp_get_uri( sprintf( 'assets/admin/js/%s.js', $script ), $script );
        $handle = 'cfp_' . $script;
        wp_enqueue_script(
            $handle,
            $url,
            $dependency,
            CFP_VERSION
        );
        return $handle;
    }
    
    /**
     * Enqueue admin specific style
     *
     * @param string $style
     * @param array $dependency
     * @return string - style handle
     */
    protected function enqueue_style( $style, $dependency = array() ){
        $style 	= preg_replace( '|([^a-z\-\_\.])|' , '', $style);
        $url 	= cfp_get_uri( sprintf( 'assets/admin/css/%s.css', $style ), $style );
        $handle = 'cfp_' . $style;
        wp_enqueue_style(
            $handle,
            $url,
            $dependency,
            CFP_VERSION
        );
        return $handle;
    }
}

/**
 * Admin class. Implements all plugin administration
 *
 * @since 1.0
 * @package Video WP plugin
 */
class CFP_Admin extends CFP_Admin_Helper{

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
		
	    // admin init
	    add_action( 'admin_init', array( $this, 'redirect_about_page' ) );
	    
		// admin menu
		add_action( 'admin_menu' , array( $this, 'admin_menu' )  );
		
		// add Settings link to plugin actions
		add_filter('plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2);
		
		// tinymce
		add_action('admin_head', array( $this, 'tinymce' ) );
		add_filter('mce_external_languages', array( $this, 'tinymce_languages' ) );
		
	}
	
	/**
	 * Redirects to about page on admin_init when plugin has just been activated
	 *
	 * @return void
	 */
	public function redirect_about_page(){
	    if( !current_user_can( 'manage_options' ) ){
	        return;
	    }
	
	    if( !get_transient( 'cfp_about_page_activated' ) ){
	        return;
	    }
	
	    delete_transient( 'cfp_about_page_activated' );
	    wp_redirect( menu_page_url( 'cfp-about', false ) );
	    die();
	}
	
	/**
	 * Add plugin admin menu
	 * @return void
	 */
	public function admin_menu() {
        add_theme_page(
            __( 'CodeFlavors Featured Post', 'cfp' ), 
            __( 'Featured Post by CodeFlavors', 'cfp' ), 
            'edit_posts', 
            'cfp-options',
            array( $this, 'plugin_options' ) );
        
        // about plugin page
        $about_page = add_submenu_page(
            null,
            __( 'About CodeFlavors Featured Post', 'cvwp' ),
            __( 'About CodeFlavors Featured Post' ),
            'manage_options',
            'cfp-about',
            array( $this, 'about_page' )
        );
        add_action( 'load-' . $about_page, array( $this, 'on_load_about_page' ) );
	}
	
	/**
	 * Output plugin page
	 */
	public function plugin_options(){
	    
	    require_once cfp_template_abs_path( 'plugin_options' ) ;
	    
	}
	
	/**
	 * Admin menu page load callback
	 *
	 *
	 * Plugin about page on load callback
	 * @return void
	 */
	public function on_load_about_page(){
	    $this->enqueue_style( 'page-about' );
	}
	
	/**
	 * Admin menu page callback
	 *
	 * Outputs about page on plugin activation
	 *
	 * @return void
	 */
	public function about_page(){
	
	    $path = cfp_template_abs_path( 'about', 'page' );
	    include_once $path;
	}
	
	/**
	 * Add extra actions links to plugin row in plugins page
	 * @param array $links
	 * @param string $file
	 */
	public function plugin_action_links( $links, $file ){
	    // add Settings link to plugin actions
	    $plugin_file = plugin_basename( CFP_PATH . '/index.php' );
	    if( $file == $plugin_file ){
	        $link = sprintf( '<a href="%s"> %s</a>', menu_page_url( 'cfp-options' , false ), __('Options', 'cfp') );
	        array_unshift( $links, $link );
	    }
	    
	    return $links;
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