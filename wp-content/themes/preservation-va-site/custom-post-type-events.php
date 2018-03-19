<?php
  add_action('init', 'pdf_resources_register');

  function pdf_resources_register() {
      $labels = array(
          'name' => _x('PDF Resource', 'post type general name'),
          'singular_name' => _x('PDF Resource', 'post type singular name'),
          'add_new' => _x('Add New', 'PDF Resource'),
          'add_new_item' => __('Add New PDF Resource'),
          'edit_item' => __('Edit PDF Resource'),
          'new_item' => __('New PDF Resource'),
          'view_item' => __('View PDF Resource'),
          'search_items' => __('Search PDF Resources'),
          'not_found' =>  __('Nothing found'),
          'not_found_in_trash' => __('Nothing found in Trash'),
          'parent_item_colon' => ''
      );

      $args = array(
          'labels' => $labels,
          'public' => true,
          'publicly_queryable' => true,
          'show_ui' => true,
          'query_var' => true,
          'rewrite' => true,
          'capability_type' => 'post',
          'hierarchical' => false,
          'menu_position' => null,
          'supports' => array('title','editor','thumbnail'),
          'rewrite' => array('slug' => 'pdf_resource', 'with_front' => FALSE)
        ); 

      register_post_type( 'pdf_resource' , $args );
  }

  add_action("admin_init", "admin_init");

  function admin_init(){
    add_meta_box("pdf-meta", "Upload", "pdf_meta", "pdf_resource", "side", "low");
  }

  function pdf_meta() {
    global $post;
    $custom = get_post_custom($post->ID);
    $pdf_file = $custom["pdf_file"][0];
    echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    ?>
    <p><label>PDF File:</label><br />
    <input type="text" name="pdf_file" id="pdf_file" value="<?php echo $pdf_file; ?>" style="width:258px">
    <input type="button" name="pdf_button" id="pdf_button" value="Browse" style="width:258px"></p> 
    <?php
  }

  register_taxonomy("pdf_category", array("pdf_resource"), array("hierarchical" => true, "label" => "Categories", "singular_label" => "Category", "rewrite" => true));
  register_taxonomy("pdf_color", array("pdf_resource"), array("hierarchical" => true, "label" => "Colors", "singular_label" => "Color", "rewrite" => true));

  function my_admin_scripts() {
  wp_enqueue_script('media-upload');
  wp_enqueue_script('thickbox');
  wp_register_script('my-upload', get_bloginfo('template_url') . '/js/my-script.js', array('jquery','media-upload','thickbox'));
  wp_enqueue_script('my-upload');
  } 
  function my_admin_styles() {
  wp_enqueue_style('thickbox');
  }
  add_action('admin_print_scripts', 'my_admin_scripts');
  add_action('admin_print_styles', 'my_admin_styles');

      add_action('save_post', 'save_details');
      function save_details(){
        global $post;

        if( $post->post_type == "pdf_resource" ) {
              update_post_meta($post->ID, "pdf_file", $_POST["pdf_file"]);
        }

      }


      /*  End custom post types */