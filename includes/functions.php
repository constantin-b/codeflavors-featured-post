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
    $templates['default'] = array(
        'output_callback' => 'cfp_output_template_default',
        'name'            => __( 'Fancy', 'cfp' )
    );
    
    // give template a unique key
    $templates['fancy'] = array(
        // register a callback function that will be called when a featured post is displayed
        'output_callback'   => 'cfp_output_template_fancy',
        'name'              => __( 'Default template', 'cfp' )
    );
    
    return $templates;
}
add_filter( 'cfp_register_template', 'cfp_register_template_default' );

/**
 * Template Fancy output function.
 * 
 * @param object $post - the post being displayed
 * @param string/HTML $image - the HTML image code to display
 */
function cfp_output_template_fancy( $post, $terms, $image, $image_url ){
    
    $t = array();
    if( $terms ){
        $term_link = get_term_link( $terms[0] );
        $t[] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', $term_link, esc_attr( $terms[0]->name ) );
        if( $terms[0]->parent ){
            $parent = get_term( $terms[0]->parent, $terms[0]->taxonomy );
            if( $parent ){
                $term_link = get_term_link( $parent );
                array_unshift( $t, sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', $term_link, esc_attr( $parent->name ) ) );
            }
        }
    }
    $categories = implode( ' > ', $t );
    
    $permalink = get_permalink( $post );
    $title = esc_attr( $post->post_title );
    
    $css_classes = array( 'codeflavors-featured-post', 'theme-fancy', 'featured-post-' . $post->ID );
    if( !$image ){
        $css_classes[] = 'no-image';
    }
    
    $class = implode( ' ' , $css_classes );
    
    $output = <<<HTML
<div class="{$class}">
    {$image}
    <div class="cf-overlay">
        <div class="cf-inside">
            <h2><a href="{$permalink}" title="{$title}">{$title}</a></h2>
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
function cfp_template_fancy_styles(){
?>
<!-- CodeFlavors Featured Post styling -->
<style type="text/css">
.codeflavors-featured-post.theme-fancy{
    display:block;
    position:relative;
    width:100%;
    height:auto;
	margin:1em 0;
}
    .codeflavors-featured-post.theme-fancy img{
        width:100%;
        max-width:100%;
        height:auto; 
    }
    .codeflavors-featured-post.theme-fancy .cf-overlay{
        position:absolute;
        bottom:0px;
        left:0px;
        width:100%;
        max-height:100%;    	
    }
        .codeflavors-featured-post.theme-fancy .cf-overlay .cf-inside{
            display:block;
        	position:relative;
        	padding:.5em 2em;
        	text-align:center;
        	color:#FFF;
        	text-transform:uppercase;
        	background-color:rgba(0,0,0,0.5);
        } 
        .codeflavors-featured-post.theme-fancy.no-image .cf-overlay .cf-inside{
        	background:#000;
        }
        
            .codeflavors-featured-post.theme-fancy .cf-overlay .cf-inside h2{
                padding:0px 0px .5em;
            	margin:.5em 0px .5em;
            	border-bottom:1px #FFF solid;
            }
                .codeflavors-featured-post.theme-fancy .cf-overlay .cf-inside h2 a{
	               text-decoration:none;
                   color:#FFF; 
                }
            .codeflavors-featured-post.theme-fancy .cf-overlay .cf-inside p{
                margin:0px;
            	padding:0px;
            }
            .codeflavors-featured-post.theme-fancy .cf-overlay .category{
                background:#000;
            	text-align:center;
            	color:#FFF;
            	text-transform:uppercase;
            	padding:.6em 0 .6em;
            }
                .codeflavors-featured-post.theme-fancy .cf-overlay .category a{
	                color:#FFF;
                	text-decoration:none;
                	border:none;
                }
</style>
<!-- end - CodeFlavors Featured Post styling -->
<?php
}
add_action( 'wp_print_styles' , 'cfp_template_fancy_styles', 54 );

/*********************
 * TEMPLATE Fancy
 *********************/

/**
 * Template Default output. The function is called by the plugin based on the registration of
 * the template made using filter 'cfp_register_template'
 * 
 * @param object $post
 * @param array $terms
 * @param HTML $image
 * @param string $image_url
 */
function cfp_output_template_default(  $post, $terms, $image, $image_url  ){
    
    $css_classes = array( 'codeflavors-featured-post', 'theme-default', 'featured-post-' . $post->ID );
    if( !$image ){
        $css_classes[] = 'no-image';
    }    
    $class = implode( ' ' , $css_classes );
    
    $permalink = get_permalink( $post );
    $title = esc_attr( $post->post_title );
    
    $comments_count = wp_count_comments( $post->ID );
    $comments = '';
    if( $comments_count->approved ){
        $comments = sprintf( _n( '%s comment' , '%s comments', $comments_count->approved, 'cfp' ), $comments_count->approved );
    }
    
    $date = get_the_date( '', $post );
    
    $author_name = get_the_author_meta( 'display_name', $post->author );
    $author_avatar = get_avatar( $post->author, 50 );
    
    $html = <<<HTML
<div class="{$class}">
    {$image}
    <div class="cf-post-details">
        <h2><a href="{$permalink}" title="{$title}">{$title}</a></h2>
        <p>{$post->post_excerpt}</p>
        <div class="cf-post-author">
            {$author_avatar} <strong>{$author_name}</strong>
        </div><!-- .cf-post-author -->
        <div class="cf-entry-meta">
            <span class="cfp-post-date">{$date}</span>
            <span class="cfp-comment-count">{$comments}</span>    
        </div><!-- .cf-entry-meta -->
    </div><!-- .cf-post-details -->
</div><!--end featured post-->
HTML;
    
    return $html;    
}

/**
 * Output the styling for theme "default"
 */
function cfp_template_default_styles(){
    ?>
<!-- CodeFlavors Featured Post styling -->
<style type="text/css">
.codeflavors-featured-post.theme-default{
    display:block;
    position:relative;
    width:100%;
    height:auto;
	margin:1em 0;
	background-color:#FFF;
	border:1px #eee solid;
}
    .codeflavors-featured-post.theme-default img{
        width:100%;
        max-width:100%;
        height:auto; 
    }
    .codeflavors-featured-post.theme-default .cf-post-details{
	   padding:.5em;
    }
        .codeflavors-featured-post.theme-default .cf-post-details h2{
	        font-size:1.2em;
            margin:.3em 0 0;
        	padding:0;
        	color:#868286;
        }
            .codeflavors-featured-post.theme-default .cf-post-details h2 a{
	            text-decoration:none;
            	border:none;
            	color:#868286;
            }
        .codeflavors-featured-post.theme-default .cf-post-details p{
	        font-size:.9em;
            padding:.5em 0 1em;
        	margin:0;
        	color:#A6A6A6;
        }
    .codeflavors-featured-post.theme-default .cf-post-author{
	    font-size:.7em;
        color:#727272;
    }
        .codeflavors-featured-post.theme-default .cf-post-author img{
	        width:auto;
        	height:auto;
        	max-height:30px;
        }  
   .codeflavors-featured-post.theme-default .cf-entry-meta{
	    font-size:.7em;
   	    color:#999;
   	    border-top:1px #EFEFEF solid;
   	    margin:.5em 0 0;
   	    padding:1em 0;
   }
       .codeflavors-featured-post.theme-default .cf-entry-meta span{
	        display:block;
       	    text-align:right;
       }  
    
</style>
<!-- end - CodeFlavors Featured Post styling -->
<?php
}
add_action( 'wp_print_styles' , 'cfp_template_default_styles', 54 );