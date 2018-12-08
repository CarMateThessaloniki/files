<?php
// iiRe Social Icons Widget - (Demo Version) - 06-12-2013
class iiReSocialMedia extends WP_Widget {

  	function iiReSocialMedia()  {
    	$widget_ops = array('classname' => 'iiReSocialMedia', 'description' => 'Social Media Icons for widget' );
    	$this->WP_Widget('iiReSocialMedia', 'iiRe Social Media Icons', $widget_ops);
  	}
 

	function form($instance)  {
    	$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    	$title = $instance['title'];
		?>
  		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>		

		<?php
	}
 

	function update($new_instance, $old_instance) {
    	$instance = $old_instance;
    	$instance['title'] = $new_instance['title'];
    	return $instance;
  	}
 

  	function widget($args, $instance) {
    	extract($args, EXTR_SKIP);

    	$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		
        echo $before_widget;
        if(!empty($title)) {
           echo $before_title.$title.$after_title;
        }
		
		global $wpdb;
		global $blog_id;

		$table_name = $wpdb->get_blog_prefix($blog_id)."iire_social";
	
		$settings = array();		
		$rs = $wpdb->get_results("SELECT * FROM $table_name");
		foreach ($rs as $row) {
			$settings[$row->option_name] = $row->option_value;
		}		
	
		$opac =	$settings['widget_icon_opacity']/100;
		
		echo '<div id="iire_social_widget" class="iire_social_widget" data-opacity="'.$opac.'" data-effect="'.$settings['widget_effect'].'" data-color="'.$settings['widget_icon_bgcolor_hover'].'" data-size="'.$settings['widget_icon_size'].'" data-spacing="'.$settings['widget_icon_spacing'].'">';
		echo stripslashes($settings['widget_output']);
		echo '</div>';

		echo $after_widget;
  	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("iiReSocialMedia");') );
?>