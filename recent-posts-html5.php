<?php
/*
 * Plugin Name: Recent Posts HTML5
 * Version: 0.0.1
 * Description: Recent Posts widget that uses HTML5
 * Author: Amber Kayle Armstrong
 * Author URI: http://www.amberkayle.com
 */
 
 
add_action( 'widgets_init', create_function('', 'return register_widget("Recent_Posts_Html5");') );


# Make sure required fields exist for this plugin
#register_activation_hook( __FILE__, array( 'Recent_Posts_Html5', 'activation_check' ) );




 
class Recent_Posts_Html5 extends WP_Widget {


  function Recent_Posts_Html5() {
    $widget_ops = array('classname' => 'widget_recent_posts_html5', 'description' => __( "Recent Posts widget that uses HTML5") );      
    $this->WP_Widget( 'recent_posts_html5', __('Recent Posts HTML5'), $widget_ops);

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );  
  
  
  }
  
  
  
  
  /* 
  Creates the edit form for the widget.
  */
	function form($instance) {
	
	  // Default values!
    $instance = wp_parse_args( (array) $instance, array(  'recent_title' => 'Recent Posts',
                                                          'recent_number' => 5 ) );
                 
    # Get current values                                                      
		$recent_title = isset($instance['recent_title']) ? esc_attr($instance['recent_title']) : 'abc';
		$recent_number = isset($instance['recent_number']) ? absint($instance['recent_number']) : 5;
		
		$output_recent = "<p><label for='{$this->get_field_id('recent_title')}'>Title:</label>";
		$output_recent .= "<input class='widefat' id='{$this->get_field_id('recent_title')}' name='{$this->get_field_name('recent_title')}'";
	  $output_recent .= "type='text' value='{$recent_title}' /></p>";
		
		

    echo $output_recent;
	
	}
	
	
	
	
  /*
  Saves the widgets settings.
  */
	function update($new_instance, $old_instance) {
	  // The old instance($old_instance) is overwritten by the new instance($new_instance) which values are taken from the widget form
    $instance = $old_instance;
    
    // strip_tags() and stripslashes() to ensure that no matter what a user puts in the widget options, 
    // they will not break the page the widget appears on.
    $instance['recent_title'] = strip_tags(stripslashes($new_instance['recent_title']));
    $instance['recent_number'] = strip_tags(stripslashes($new_instance['recent_number']));

   return $instance;
	}
	
	
	
  /*
  Displays the widget
  */
	function widget($args, $instance) {
		
		// create native WP variables.. such as  $before_widget, $before_title, $after_title, and $after_widget
		extract($args);
		
		// extract widget config options. 
		// use if statement to ensure that if the string was empty that the default will be used
#    $blogs_per_page = empty($instance['blogs_per_page']) ? '5' : $instance['blogs_per_page'];
    // $title has a special filter applied because it is the title of the widget which WordPress recognizes.
    $recent_title = apply_filters('widget_title', empty($instance['recent_title']) ? '&nbsp;' : $instance['recent_title']);

    # Before the widget
    echo $before_widget;
    
    # The title
    if ( $recent_title ){
     echo $before_title . $recent_title . $after_title;
    }    
    
    
    # After the widget
    echo $after_widget;    
    
  }
		  


}



 
 
?>
