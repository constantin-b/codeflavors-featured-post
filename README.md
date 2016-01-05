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

## Basic HTML structure

The plugin allows other HTML structure, the process is described under **Creating a new template**. For theme **default**, the HTML structure is the following.

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