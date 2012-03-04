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
    $instance = wp_parse_args( (array) $instance, array(  'widget_title' => 'Recent Posts',
                                                          'recent_number' => 5,
                                                          'include_author' => true,
                                                          'recent_title' => 'Untitled Post' ) );
                 
    # Get current values or set to defaults                                        
		$widget_title = esc_attr($instance['widget_title']);
		$recent_number = absint($instance['recent_number']);
    $include_author =  (bool) $instance['include_author'];	
    $recent_title = esc_attr($instance['recent_title']);	
		
		
		
		# Widget Title
		$output_title = "<p style='text-align:left'>";
    $output_title .= '<label for="' . $this->get_field_name('widget_title') . '">' . __('Widget Title: ');		
		$output_title .= "<input id='{$this->get_field_id('widget_title')}' name='{$this->get_field_name('widget_title')}'";
	  $output_title .= "type='text' value='{$widget_title}' />";
	  $output_title .= "</label></p>";
		
		
		# Widget Title
		$output_name = "<p style='text-align:left'>";
    $output_name .= '<label for="' . $this->get_field_name('recent_title') . '">' . __('Default Post Title: ');		
		$output_name .= "<input id='{$this->get_field_id('recent_title')}' name='{$this->get_field_name('recent_title')}'";
	  $output_name .= "type='text' value='{$recent_title}' />";
	  $output_name .= "</label></p>";		
		
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
    echo $output_name;
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
    $instance['widget_title'] = strip_tags(stripslashes($new_instance['widget_title']));
    $instance['recent_number'] = strip_tags(stripslashes($new_instance['recent_number']));
    $instance['recent_title'] = strip_tags(stripslashes($new_instance['recent_title']));
    
    # Include author = checkbox
    $instance['include_author'] = 0;
    if( isset( $new_instance['include_author'] ) ){
      $instance['include_author'] = 1;
    }          
    
    
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');    
    
   return $instance;
	}
	
	
	
  /*
  Displays the widget
  */
	function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_posts_html5', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
		
		// extract widget config options. 
    // $title has a special filter applied because it is the title of the widget which WordPress recognizes.
    $widget_title = apply_filters('widget_title', empty($instance['widget_title']) ? '&nbsp;' : $instance['widget_title']);
    $recent_number = empty($instance['recent_number']) ? '5' : $instance['recent_number'];
    $include_author = (isset($instance['include_author']) && $instance['include_author']) ? true : false;
    $recent_title = empty($instance['recent_title']) ? '&nbsp;' : $instance['recent_title'];
    
    
    # Before the widget
    echo $before_widget;
    
    # The title
    if ( $widget_title ){
     echo $before_title . $widget_title . $after_title;
    }    
    
    // Get posts 
    $query = new WP_Query(array('posts_per_page' => $recent_number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true));
    
      
    // Gather post output
    $output = "<ul class='recent-posts-html5'>";
    while ($query->have_posts()){
      $query->the_post();
      
      $permalink = get_permalink();
      $post_title = esc_attr(get_the_title() ? get_the_title() : $recent_title);

      
      $output .= "<li><a href='$permalink'>";
      $output .= "<cite>$post_title</cite>";
      if( $include_author ){
        $author = get_the_author();
        $output .= "<dt>$author</dt>";
      } 
      $output .= "</a></li>";
      
    }
    $output .= "</ul>";
    echo $output;
    
    
    # After the widget
    echo $after_widget;    
    
    // Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();    
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_posts', $cache, 'widget');    
  }
  
  
	function flush_widget_cache() {
		wp_cache_delete('widget_recent_posts', 'widget');
	}  
		  


}



 
 
?>
