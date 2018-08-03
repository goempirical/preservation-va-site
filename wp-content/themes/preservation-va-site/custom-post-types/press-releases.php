<?php 
if ( ! function_exists('press_release_cpt') ) {

// Register Custom Post Type
function press_release_cpt() {

  $labels = array(
    'name'                  => _x( 'Press Releases', 'Post Type General Name', 'pva' ),
    'singular_name'         => _x( 'Press Release', 'Post Type Singular Name', 'pva' ),
    'menu_name'             => __( 'Press Releases', 'pva' ),
    'name_admin_bar'        => __( 'Press Release', 'pva' ),
    'archives'              => __( 'Press Release Archives', 'pva' ),
    'attributes'            => __( 'Press Release Attributes', 'pva' ),
    'parent_item_colon'     => __( 'Parent Press Release:', 'pva' ),
    'all_items'             => __( 'All Press Releases', 'pva' ),
    'add_new_item'          => __( 'Add New Press Release', 'pva' ),
    'add_new'               => __( 'Add New', 'pva' ),
    'new_item'              => __( 'New Press Release', 'pva' ),
    'edit_item'             => __( 'Edit Press Release', 'pva' ),
    'update_item'           => __( 'Update Press Release', 'pva' ),
    'view_item'             => __( 'View Press Release', 'pva' ),
    'view_items'            => __( 'View Press Releases', 'pva' ),
    'search_items'          => __( 'Search Press Release', 'pva' ),
    'not_found'             => __( 'Not found', 'pva' ),
    'not_found_in_trash'    => __( 'Not found in Trash', 'pva' ),
    'featured_image'        => __( 'Featured Image', 'pva' ),
    'set_featured_image'    => __( 'Set featured image', 'pva' ),
    'remove_featured_image' => __( 'Remove featured image', 'pva' ),
    'use_featured_image'    => __( 'Use as featured image', 'pva' ),
    'insert_into_item'      => __( 'Insert into Press Release', 'pva' ),
    'uploaded_to_this_item' => __( 'Uploaded to this Press Release', 'pva' ),
    'items_list'            => __( 'Press Releases list', 'pva' ),
    'items_list_navigation' => __( 'Press Releases list navigation', 'pva' ),
    'filter_items_list'     => __( 'Filter Press Releases list', 'pva' ),
  );
  $args = array(
    'label'                 => __( 'Press Release', 'pva' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
    'taxonomies'            => array( 'category', 'post_tag' ),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'menu_icon'             => 'dashicons-media-text',
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'capability_type'       => 'post',
  );
  register_post_type( 'press_release', $args );

}
add_action( 'init', 'press_release_cpt', 0 );

}