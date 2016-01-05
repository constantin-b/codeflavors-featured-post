<?php
// No direct access
if( !defined( 'ABSPATH' ) ){
	die();
}

// add WP 3.9.0 wp_normalize_path if unavailable
if( !function_exists('wp_normalize_path') ){
	/**
	 * Normalize a filesystem path.
	 *
	 * Replaces backslashes with forward slashes for Windows systems,
	 * and ensures no duplicate slashes exist.
	 *
	 * @since 3.9.0
	 *
	 * @param string $path Path to normalize.
	 * @return string Normalized path.
	 */
	function wp_normalize_path( $path ) {
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '|/+|','/', $path );
		return $path;
	}
}

/**
 * Returns absolute path within the plugin for a given relative path.
 *
 * @param string $rel_path
 * @return string - complete absolute path within the plugin
 */
function cfp_path( $rel_path = '' ){
	$path = path_join( CFP_PATH, $rel_path );
	return wp_normalize_path( $path );	
}

/**
 * Generates a complete URL to files located inside the plugin folder.
 * 
 * @param string $rel_path - relative path to file
 * @return string - complete URL to file
 */
function cfp_get_uri( $rel_path = '' ){
	$uri 	= is_ssl() ? str_replace('http://', 'https://', CFP_URL) : CFP_URL;	
	$path 	= path_join( $uri, $rel_path );
	return $path;
}

/**************************************************************************
 * Register new templates
 **************************************************************************/

/**
 * Add new templating function using plugin filter 'cfp_register_template'
 * @param array $templates
 */
function cfp_register_template_default( $templates ){
    // give template a unique key
    $templates['default'] = array(
        // register a callback function that will be called when a featured post is displayed
        'output_callback' => 'cfp_output_template_default' 
    );   
    return $templates;
}
add_filter( 'cfp_register_template', 'cfp_register_template_default' );

/**
 * Template default output function. The function is called by the plugin based on the registration of
 * the template made using filter 'cfp_register_template'
 * 
 * @param object $post - the post being displayed
 * @param string/HTML $image - the HTML image code to display
 */
function cfp_output_template_default( $post, $terms, $image ){
    
    $t = array();
    if( $terms ){
        $t[] = $terms[0]->name;
        if( $terms[0]->parent ){
            $parent = get_term( $terms[0]->parent, $terms[0]->taxonomy );
            if( $parent ){
                array_unshift( $t, $parent->name );
            }
        }
    }
    $categories = implode( ' > ', $t );
    
    $output = <<<HTML
<div class="codeflavors-featured-post">
    {$image}
    <div class="cf-overlay">
        <div class="cf-inside">
            <h2>{$post->post_title}</h2>
            <p>{$post->post_excerpt}</p>            
        </div><!-- .cf-inside -->
        <div class="category">
            {$categories}
        </div>
    </div><!-- .cf-overlay -->
</div><!--end featured post-->
HTML;
    
    // always return, don't echo the output
    return $output;
}

/**
 * Output the styling for theme "default"
 */
function cfp_template_default_styles(){
?>
<!-- CodeFlavors Featured Post styling -->
<style type="text/css">
.codeflavors-featured-post{
    display:block;
    position:relative;
    width:100%;
    height:auto;
}
    .codeflavors-featured-post img{
        width:100%;
        max-width:100%;
        height:auto; 
    }
    .codeflavors-featured-post .cf-overlay{
        position:absolute;
        bottom:0px;
        left:0px;
        width:100%;
        background-color:rgba(0,0,0,0.5);    	
    }
        .codeflavors-featured-post .cf-overlay .cf-inside{
            display:block;
        	position:relative;
        	padding:.5em 2em;
        	text-align:center;
        	color:#FFF;
        	text-transform:uppercase;
        } 
            .codeflavors-featured-post .cf-overlay .cf-inside h2{
                padding:0px 0px .5em;
            	margin:.5em 0px .5em;
            	border-bottom:1px #FFF solid;
            }
            .codeflavors-featured-post .cf-overlay .cf-inside p{
                margin:0px;
            	padding:0px;
            }
            .codeflavors-featured-post .cf-overlay .category{
                background:#000;
            	text-align:center;
            	color:#FFF;
            	text-transform:uppercase;
            	padding:.6em 0 .6em;
            }
</style>
<!-- end - CodeFlavors Featured Post styling -->
<?php
}
add_action( 'wp_print_styles' , 'cfp_template_default_styles', 54 );