<?php
  add_action('init', 'event_resources_register');

  function event_resources_register() {
      $labels = array(
          'name' => _x('Events', 'post type general name'),
          'singular_name' => _x('Event', 'post type singular name'),
          'add_new' => _x('Add', 'event'),
          'add_new_item' => __('Add event'),
          'edit_item' => __('Edit event'),
          'new_item' => __('New event'),
          'view_item' => __('View event'),
          'search_items' => __('Search events'),
          'not_found' =>  __('Nothing found'),
          'not_found_in_trash' => __('Nothing found in Trash'),
          'parent_item_colon' => ''
      );

      $args = array(
          'labels' => $labels,
          'taxonomies' => array('historic_sites'),
          'public' => true,
          'publicly_queryable' => true,
          'show_ui' => true,
          'query_var' => true,
          'rewrite' => true,
          'capability_type' => 'post',
          'hierarchical' => false,
          'menu_position' => 5,
          'menu_icon'             => 'dashicons-calendar-alt',
          'supports' => array('title', 'editor', 'thumbnail'),
          'has_archive' => true
        );

      register_post_type('events' , $args);
  }

  add_action("admin_init", "admin_init");

  function admin_init (){
    add_meta_box("event-meta", "Event date", "event_date_field", "events", "side", "low");
  }

  function event_date_field () {
    global $post;
    $custom = get_post_custom($post->ID);
    $pdf_file = $custom["pdf_file"][0];
    echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
      Date field goes here!
    <?php
  }

  function my_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_register_script('my-upload', get_bloginfo('template_url') . '/js/my-script.js', array('jquery','media-upload','thickbox'));
    wp_enqueue_script('my-upload');
  }
  function my_admin_styles () {
    wp_enqueue_style('thickbox');
  }
  add_action('admin_print_scripts', 'my_admin_scripts');
  add_action('admin_print_styles', 'my_admin_styles');

  add_action('save_post', 'save_details');
  function save_details () {
    global $post;

    if ( $post->post_type == "event" ) {
      update_post_meta($post->ID, "pdf_file", $_POST["pdf_file"]);
    }
  }

  /*  End custom post types */

add_action('pre_get_posts', function($query) {
  if ( ! is_admin() && $query->is_main_query() ) {

      if ( is_archive() && ( is_tax('our_work') || is_tax('historic_sites') ) ) {
          $query->set( 'post_type', array( 'post', 'events' ) );
      }

  }
});
