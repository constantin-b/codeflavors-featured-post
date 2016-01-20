<div class="wrap">
    <h1><?php _e( 'CodeFlavors Featured Post', 'cfp' );?></h1>
    
    <p>
        <?php _e( 'Below you can find a brief description of the shortcode implemented by the plugin.', 'cfp' );?><br />
        <?php printf( __( 'For more information about this plugin, please see the %sdocumentation%s.', 'cfp' ), '<a href="'.cfp_plugin_url( 'documentation/wordpress-featured-post/' ).'">', '</a>' );?>
    </p>
      
    <?php 
        $shortcodes = cfp_get_shortcodes();
        foreach( $shortcodes as $shortcode => $data ):
    ?>
    
    <h2>[<?php echo $shortcode;?>]</h2>
    
    <ol>
        <?php foreach( $data['atts'] as $attr => $details ):?>
        <li>
            <strong><?php echo $attr;?></strong> : <?php echo $details['desc'];?> <em>( <?php _e( 'default', 'cfp' )?> : <?php cfp_value_to_txt( $details['value'] );?> )</em>
        </li>
        <?php endforeach;?>
    </ol>
    
    <?php endforeach;?> 
     
</div>