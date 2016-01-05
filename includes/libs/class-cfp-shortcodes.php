<?php
// No direct include
if( !defined('ABSPATH') ){
	die();
}

/**
 * Shortcodes class. Implements all plugin shortcodes
 *
 * @since 1.0
 * @package Video WP plugin
 */
class CFP_Shortcodes{
	
	/**
	 * @var instance
	 **/
	private static $instance = null;
	
	private $templates = array();
	
	static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new CFP_Shortcodes;
		}
		
		return self::$instance;
	}
	
	/**
	 * Constructor, implements the shortcodes
	 */
	private function __construct(){
	    
	    if( !isset( self::$instance ) ){
	       self::$instance = $this;
	    }
	    
	    // allow third party plugins and themes to register new templating functions
	    $this->templates = apply_filters( 'cfp_register_template', $this->templates );
	    
	    // register the shortcodes
	    $shortcodes = $this->shortcodes();
		foreach( $shortcodes as $tag => $data ){
			add_shortcode( $tag , $data['callback'] );
		}
	}
	
	/**
	 * Contains all shortcodes implementations
	 *
	 * @param strong $shortcode - return a single shortcode by key
	 * @return array
	 */
	private function shortcodes( $shortcode = false ){
		$shortcodes = array();	
		
		// remove this and replace with your own shortcodes
		$shortcodes['codeflavors_featured_post'] = array(
			'callback' => array( self::$instance, 'featured_post' ),
			'atts' => array(
				'category' => array(
				    'desc'  => __( 'The category/taxonomy ID to retrieve the latest post from.', 'cfp' ),
				    'value' =>   0
				    
				),
				'taxonomy' => array(
				    'desc' => __( 'The taxonomy name, if retrieving from anything else other than regular categories.', 'cfp' ),
				    'value' =>  ''
				    
				),
			    'post_type' => array(
			        'desc' => __( 'The post type that should be retrieved from the given taxonomy. Not required when featuring regular posts.', 'cfp' ),
			        'value' => ''
			    ),
			    'post_num' => array(
			        'desc' => __( 'The number of posts to retrieve.', 'cfp' ),
			        'value' => 1
			    ),
			    'offset' => array(
			        'desc' => __( 'First row to return (ie. a value of 1 means it will return starting with the second most recent post).', 'cfp' ),
			        'value' => 0
			    ),
				'post_id'  => array(
				    'desc' => __( 'A post ID. This will retrieve the exact post ID and will override all other parameters if set.', 'cfp' ),
				    'value' => 0
				),
				'template' => array(
				    'desc' => __( 'The featured post template to use when displaying a post.', 'cfp' ),
				    'value' => 'default'
				),
			)
		);
		
		if( $shortcode ){
			if( array_key_exists( $shortcode , $shortcodes ) ){
				return $shortcodes[ $shortcode ];
			}else{
				return false;
			}
		}		
		return $shortcodes;
	}
	
	/**
	 * Demo shortcode implementation
	 *
	 * @param array $atts
	 * @return string;
	 */
	public function featured_post( $atts = array() ){
		// get shortcode details
		$data = $this->get_shortcode_atts( 'codeflavors_featured_post' );
		// merge user attributes with defaults
		$atts = extract( shortcode_atts( 
			$data, 
			$atts 
		), EXTR_SKIP );
		
		// set a default for the currently extracted post
		$posts = false;
		
		// if post ID is set, extract the post
		if( $post_id ){
		    $post = get_post( $post_id );
		    if( $post && !is_wp_error( $post ) ){
		        $posts = array( $post );
		    }		    
		}else{
		    // the following options are: get the latest post type OR get the latest post type from category
		    $args = array(
		        'posts_per_page'      => $post_num,
		        'offset'              => $offset,
		        'suppress_filters'    => false,
		        'post_status'         => 'publish',
		        'orderby'             => 'post_date',
		        'order'               => 'DESC',
		        'post_type'           => ( $post_type ? $post_type : 'post' )		        
		    );
		    
		    if( $category ){
		        $args['tax_query'][] = array(
	                'taxonomy' 	=> ( !empty( $taxonomy ) ? $taxonomy : 'category' ),
					'field'		=> 'id',
					'terms'		=> $category
	            );		        
		    }
		    
		    $posts = get_posts( $args );		    
		}
		
		if( !$posts || is_wp_error( $posts ) ){
		    return;
		}
		
		// the output
		$output   = '';
		$template = !empty( $template ) ? $template : 'default';
		// get post taxonomies
		$taxonomy = !empty( $taxonomy ) ? $taxonomy : 'category';
		// iterate posts
		foreach( $posts as $post ){
		    $terms = wp_get_post_terms( $post->ID, $taxonomy );		  
            // get post featured image
            $image = false;
            $post_thumbnail_id = get_post_thumbnail_id( $post->ID );
            if( $post_thumbnail_id ){
                $image = wp_get_attachment_image( $post_thumbnail_id, 'full' );
            }
            
            if( array_key_exists(  $template, $this->templates ) ){
                $output .= call_user_func( $this->templates[ $template ]['output_callback'], $post, $terms, $image );
            }else{
                // template not found, maybe show an error?
            }		    
		}
		
		// return output
		return $output;
	}
	
	/**
	 * Return the shortcode default attributes as a simple associative array
	 * @param string $shortcode
	 */
	private function get_shortcode_atts( $shortcode ){
	    $data = $this->shortcodes( $shortcode );
	    $result = array();
	    if( $data ){
	        foreach( $data['atts'] as $attr => $details ){
	            $result[ $attr ] = $details['value'];
	        }
	    }
	    return $result;
	}
	
	/**
	 * Returns all registered shortcodes
	 * @return array
	 */
	static function get_shortcodes(){
		return self::init()->shortcodes();
	}
}
CFP_Shortcodes::init();