# CodeFlavors featured post
WordPress plugin that features any post type by using a simple shortcode. 

##Shortcode structure

[codeflavors_featured_post]

###Attributes

* *category* : The category/taxonomy ID to retrieve the latest post from. ( default : 0 )
* *taxonomy* : The taxonomy name, if retrieving from anything else other than regular categories. ( default : )
* *post_type* : The post type that should be retrieved from the given taxonomy. Not required when featuring regular posts. ( default : )
* *post_num* : The number of posts to retrieve. ( default : 1 )
* *offset* : First row to return (ie. a value of 1 means it will return starting with the second most recent post). ( default : 0 )
* *post_id* : A post ID. This will retrieve the exact post ID and will override all other parameters if set. ( default : 0 )
* *template* : The featured post template to use when displaying a post. ( default : default )


