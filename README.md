# CodeFlavors featured post
WordPress plugin that features any post type by using a simple shortcode. 

##Shortcode structure

[codeflavors_featured_post]

###Attributes

* *category* : The category/taxonomy ID to retrieve the latest post from. ( default : 0 )
* *taxonomy* : The taxonomy name, if retrieving from anything else other than regular categories. ( default : '' )
* *post_type* : The post type that should be retrieved from the given taxonomy. Not required when featuring regular posts. ( default : '' )
* *post_num* : The number of posts to retrieve. ( default : 1 )
* *offset* : First row to return (ie. a value of 1 means it will return starting with the second most recent post). ( default : 0 )
* *post_id* : A post ID. This will retrieve the exact post ID and will override all other parameters if set. ( default : 0 )
* *template* : The featured post template to use when displaying a post. ( default : default )

##Examples

Feature latest post from all posts: 
```[codeflavors_featured_post]```

Feature latest post from a given post type **post** category that has ID 3: 
```[codeflavors_featured_post category="3"]```

Feature latest post for a custom post type **video** having taxonomy **video-cat** and term ID 2 : 
```[codeflavors_featured_post post_type="video" taxonomy="video-cat" category="2"]```

##Basic HTML structure

The plugin allows other HTML structure, the process is described under **How to create a new template**. For theme **default**, the HTML structure is the following.

```
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
```

The CSS can be modified from WP theme styles.css.

##How to create a new template

In order to allow new, custom templates to be implemented, in your WordPress theme *functions.php* file you need to hook to a filter implemented by the plugin and register your template.

```
/**
 * Add new templating function using plugin filter 'cfp_register_template'
 * @param array $templates
 */
function my_template_registration( $templates ){
    // give template a unique key
    $templates['my-template-name'] = array(
        // register a callback function that will be called when a featured post is displayed
        'output_callback' => 'my_output_template' 
    );   
    return $templates;
}
add_filter( 'cfp_register_template', 'my_template_registration' );
```

Next, you need to create the template output function that you registered, respectively `my_output_template`.

```
/**
 * Template output function. The function is called by the plugin based on the registration of
 * the template made using filter 'cfp_register_template'
 * 
 * @param object $post - the post being displayed
 * @param array $terms - the post terms
 * @param string/HTML $image - the HTML image code to display
 */
function my_output_template( $post, $terms, $image ){
    
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
```

The above function is from plugin theme *default*, you can adapt and/or change the output to your liking, based on your design.

Last step is to add the CSS styling into your WordPress theme *style.css* or where you might find it appropriate. 

In order to use this newly created theme with the shortcode, you should specify the template like this:

***
[codeflavors_featured_post template="my-template-name"]
***
