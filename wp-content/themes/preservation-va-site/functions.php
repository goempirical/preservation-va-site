<?php
function understrap_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	// Get the theme data
	$the_theme = wp_get_theme();
    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_script( 'jquery');
	wp_enqueue_script( 'popper-scripts', get_template_directory_uri() . '/js/popper.min.js', array(), false);
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get( 'Version' ), true );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

include 'custom-post-types/events.php';
include 'taxonomies/index.php';
include 'custom-page-templates-fields/custom-fields.php';

function understrap_footer_menu() {
    register_nav_menu('hictoricmenu',__( 'Hictoric Mega Menu' ));
    register_nav_menu('footermenu',__( 'Footer Menu' ));
}
add_action( 'init', 'understrap_footer_menu' );

add_image_size('banner_size', 1024, 339, true);
add_image_size('Home Banner', 1024, 514, true);

// Build Table for Plugin https://wordpress.org/plugins/advanced-custom-fields-table-field/
function buildTable($table) {
    if ( $table ) {
        echo '<table class="table" border="0">';
            if ( $table['header'] ) {
                echo '<thead>';
                    echo '<tr>';
                        foreach ( $table['header'] as $th ) {
                            echo '<th>';
                                echo $th['c'];
                            echo '</th>';
                        }
                    echo '</tr>';
                echo '</thead>';
            }
    
            echo '<tbody>';
                foreach ( $table['body'] as $tr ) {
                    echo '<tr>';
                        foreach ( $tr as $td ) {
                            echo '<td>';
                                echo $td['c'];
                            echo '</td>';
                        }
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }
}

// Add support to files upload SVG
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function understrap_all_excerpts_get_more_link( $post_excerpt ) {
	return $post_excerpt . '';
}
add_filter( 'wp_trim_excerpt', 'understrap_all_excerpts_get_more_link' );

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

// Get Image Description
function wp_get_attachment( $attachment_id ) {
    $attachment = get_post( $attachment_id );
    return array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink( $attachment->ID ),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}
    
define( 'THEME_IMG_PATH', get_stylesheet_directory_uri() . '/img/' );
?>
