<?php
/*
 * Plugin Name: Recent Posts HTML5
 * Version: 0.0.1
 * Description: Recent Posts widget that uses HTML5
 * Author: Amber Kayle Armstrong
 * Author URI: http://www.amberkayle.com
 */
 
 
add_action( 'widgets_init', create_function('', 'return register_widget("Recent_Posts_Html5");') );


 
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
	
	  // Set any uninitialized args to default values
    $instance = wp_parse_args( (array) $instance, array(  'recent_title' => 'Recent Posts',
                                                          'recent_number' => 5,
                                                          'include_author' => true ) );
                 
    # Get current values or set to defaults                                        
		$recent_title = isset($instance['recent_title']) ? esc_attr($instance['recent_title']) : 'abc';
		$recent_number = isset($instance['recent_number']) ? absint($instance['recent_number']) : 5;
    $include_author = isset($instance['include_author']) ? (bool) $instance['include_author'] : true;		
		
		
		
		# Widget Title
		$output_title = "<p style='text-align:left'>";
    $output_title .= '<label for="' . $this->get_field_name('recent_title') . '">' . __('Title: ');		
		$output_title .= "<input id='{$this->get_field_id('recent_title')}' name='{$this->get_field_name('recent_title')}'";
	  $output_title .= "type='text' value='{$recent_title}' />";
	  $output_title .= "</label></p>";
		
		
		# Number of posts to list
    // dropdown: number of blogs to display at one time
    $output_number = '<p style="text-align:left;">';
    $output_number .= '<label for="' . $this->get_field_name('recent_number') . '">' . __('Number of posts to display: ');
    $output_number .= '<select id="' . $this->get_field_id('recent_number') . 
                                  '" name="' . $this->get_field_name('recent_number') . '"> ';
    for( $i = 1; $i <=10; ++$i ){
      $selected =  ($recent_number == $i ? ' selected="selected"' : '' );
      $output_number  .= '<option value="' . $i . '"' .  $selected . '>' . $i .  '</option>';
    }		
    $output_number .= '</label></select></p>';	
		
		
    # Include Author
    $output_include_author = '<p style="text-align:left;">';    
    $output_include_author .= '<label for="' . $this->get_field_id('include_author') . '">' . __('Include Author? ');
    $output_include_author .= '<input type="checkbox" id="' . $this->get_field_id('include_author') . 
                                                     '" name="' . $this->get_field_name('include_author') . '"';
    if( $include_author ){
      $output_include_author .= ' checked="checked" ';
    }
    $output_include_author .= '/>';
    $output_number .= '</label></p>';	
    
    		

    echo $output_title;
    echo $output_number;
    echo $output_include_author;
	
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
    
    # Include author = checkbox
    $instance['include_author'] = 0;
    if( isset( $new_instance['include_author'] ) ){
      $instance['include_author'] = 1;
    }          
    
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
