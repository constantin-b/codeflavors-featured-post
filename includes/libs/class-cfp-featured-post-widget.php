<?php
// No direct include
if( !defined('ABSPATH') ){
    die();
}

/**
 * Featured Post widget
 * @author CodeFlavors
 *
 */
class CFP_Featured_Post_Widget extends WP_Widget{
    
   private $widget_data = null; 
   private $defaults = null;
    
   /**
    * Constructor, registers the new widget
    */
    public function __construct(){
        /* Widget settings. */
        $widget_opts = array(
            'classname' => 'cfp-featured-post',
            'description' => __( 'Add a CodeFlavors Featured Post widget', 'cfp' ) );
        
        /* Widget control settings. */
        $control_opts = array( 'id_base' => 'cfp-widget' );
        
        /* Create the widget. */
        parent::__construct(
            'cfp-widget',
            __( 'CodeFlavors Featured Post', 'cfp' ),
            $widget_opts,
            $control_opts
        );
        
        // get the shortcode attributes
        $obj = new CFP_Shortcodes();
        $this->widget_data  = $obj->shortcodes( 'codeflavors_featured_post' );
        $this->defaults     = $obj->get_atts( 'codeflavors_featured_post' );
        $this->defaults['title'] = '';
    }
    
    /**
     * (non-PHPdoc)
     * @see WP_Widget::widget()
     */
    public function widget( $args, $instance ){
        
        extract( $args, EXTR_SKIP );
        
        // output HTML before widget as set by sidebar
        echo $before_widget;
        
        // output the widget title
        $title = apply_filters('widget_title', $instance['title'] );
        if( $instance['title'] ){
            // output the widget title
            echo $before_title . $title . $after_title;
        }
        
        // show output
        echo call_user_func( $this->widget_data['callback'], $instance );
        
        // output HTML after widget as set by sidebar
        echo $after_widget;
    }
    
    /**
     * (non-PHPdoc)
     * @see WP_Widget::form()
     */
    public function form( $instance ){
        
       extract( wp_parse_args( $instance, $this->defaults ), EXTR_SKIP );      
?>
<p>
    <label for="<?php echo $this->get_field_id( 'title' );?>"><?php _e( 'Widget title', 'cfp' );?>: </label>
    <input class="widefat" name="<?php echo $this->get_field_name( 'title' );?>" id="<?php echo $this->get_field_id( 'title' );?>" value="<?php echo $title;?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'post_type' );?>"><?php _e( 'Post type', 'cfp' );?>: </label>
    <?php 
        $args = array(
            'name' => $this->get_field_name( 'post_type' ),
            'id'    => $this->get_field_id( 'post_type' ),
            'class' => 'widefat',
            'selected' => $post_type,
            'hide_if_empty' => false,
            'echo' => true,
            'select_opt' => false
        );
        cfp_post_types_dropdown( $args );
    ?>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'taxonomy' );?>"><?php _e( 'Taxonomy', 'cfp' );?>: </label>
    <?php 
        $args = array(
            'name' => $this->get_field_name( 'taxonomy' ),
            'id'    => $this->get_field_id( 'taxonomy' ),
            'class' => 'widefat',
            'selected' => $taxonomy,
            'hide_if_empty' => false,
            'echo' => true,
            'select_opt' => __( 'None selected', 'cfp' )
        );
        cfp_taxonomies_dropdown( $args );
    ?>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'category' );?>"><?php _e( 'Category/Term', 'cfp' );?>: </label>
    <input class="widefat" name="<?php echo $this->get_field_name( 'category' );?>" id="<?php echo $this->get_field_id( 'category' );?>" value="<?php echo $category;?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'post_num' );?>"><?php _e( 'Number of posts', 'cfp' );?>: </label>
    <input class="widefat" name="<?php echo $this->get_field_name( 'post_num' );?>" id="<?php echo $this->get_field_id( 'post_num' );?>" value="<?php echo $post_num;?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'offset' );?>"><?php _e( 'Posts offset', 'cfp' );?>: </label>
    <input class="widefat" name="<?php echo $this->get_field_name( 'offset' );?>" id="<?php echo $this->get_field_id( 'offset' );?>" value="<?php echo $offset;?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'post_id' );?>"><?php _e( 'Post ID (will override all other options)', 'cfp' );?>: </label>
    <input class="widefat" name="<?php echo $this->get_field_name( 'post_id' );?>" id="<?php echo $this->get_field_id( 'post_id' );?>" value="<?php echo $post_id;?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'template' );?>"><?php _e( 'Template', 'cfp' );?>: </label>
    <?php 
        $args = array(
            'name' => $this->get_field_name( 'template' ),
            'id'    => $this->get_field_id( 'template' ),
            'class' => 'widefat',
            'selected' => $template,
            'hide_if_empty' => false,
            'echo' => true,
            'select_opt' => false
        );
        cfp_templates_dropdown( $args );
    ?>
</p>
<?php
    }
    
    /**
     * (non-PHPdoc)
     * @see WP_Widget::update()
     */
    public function update( $new_instance, $old_instance ){
        $instance = array();
        $defaults = $this->defaults;
        
        foreach( $defaults as $field => $value ){
            $type = gettype( $value );
            switch( $type ){
                case 'integer':
                    if( isset( $new_instance[ $field ] ) ){
                        $defaults[ $field ] = absint( $new_instance[ $field ] );
                    }
                    break;
                case 'string':
                    $defaults[ $field ] = $new_instance[ $field ];
                    break;
            }
        }
        
        return $defaults;
    }    
}