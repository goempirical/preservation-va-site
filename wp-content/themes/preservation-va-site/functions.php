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
include 'custom-post-types/press-releases.php';
include 'taxonomies/index.php';
//include 'custom-page-templates-fields/custom-fields.php';

function understrap_footer_menu() {
    register_nav_menu('historicmenu',__( 'Historic Mega Menu' ));
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

// register google map API for acf
function my_acf_init() {
    
    acf_update_setting('google_api_key', 'AIzaSyBGoU3uwZTeYotBXgkkUKL0ipu6l6CfiGs');
}

add_action('acf/init', 'my_acf_init');

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

add_filter('wp_nav_menu_items', 'add_search_form', 10, 2);

function add_search_form($items, $args) {
    if( $args->theme_location == 'primary' ) {
        $search_query = get_search_query(); 
        $search_query = $search_query === 'search' ? '' : $search_query;
        $class = !$search_query || $search_query === '' ? 'closed' : 'open';
        $items .= '<li class="search '.$class.'"><form class="header__search" action="'.home_url( '/' ).'" method="get">
        <label for="s" class="ui-hidden">Search</label>
        <input autocomplete="off" id="s" name="s" type="text" placeholder="Search">
        <button type="submit">
          <span aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 21 21"><defs><path id="a" d="M0 21V0h21v21z"/></defs><g fill="none" fill-rule="evenodd"><mask id="b" fill="#fff"><use xlink:href="#a"/></mask><path fill="#FFF" d="M8.71 3.067a5.61 5.61 0 0 1 5.626 5.635 5.61 5.61 0 0 1-5.626 5.635 5.612 5.612 0 0 1-5.634-5.635A5.612 5.612 0 0 1 8.71 3.067M8.71 0C3.92 0 0 3.91 0 8.702c0 4.791 3.92 8.712 8.71 8.712 1.842 0 3.548-.59 4.954-1.57l4.71 4.7a1.528 1.528 0 0 0 2.177 0 1.533 1.533 0 0 0 0-2.169l-4.71-4.71a8.628 8.628 0 0 0 1.57-4.963c0-4.791-3.92-8.702-8.7-8.702" mask="url(#b)"/></g></svg>
          </span>
          <span class="ui-hidden">
            Submit
          </span>
        </button>
</form></li>';
    }
    return $items;
}

add_filter( 'tiny_mce_before_init', 'pva_tiny_mce_before_init' );

function pva_tiny_mce_before_init( $settings ) {

  $style_formats = array(
      array(
          'title' => 'Button Link',
          'classes' => 'btn',
          'inline' => 'a'
      )
  );
  
    if ( isset( $settings['style_formats'] ) ) {
      $orig_style_formats = json_decode($settings['style_formats'],true);
      $style_formats = array_merge($orig_style_formats,$style_formats);
    }

    $settings['style_formats'] = json_encode( $style_formats );
    return $settings;
}

function pr_archive($atts){
    extract(shortcode_atts(array(
      'count' => 2
    ), $atts));

    $args = array(
        'post_type' => 'press_release',
        'order' => 'DESC',
        'posts_per_page' => -1
    );

    $pr_query = new wp_query( $args );

    $return_string = '<ul class="pr-items">';

    while( $pr_query->have_posts() ) {
        $pr_query->the_post();
        $return_string .= '<li>';
        $return_string .= '<p class="date">'.get_the_date( 'F j, Y' ).'</p>';
        $return_string .= '<h4><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
        $return_string .= '</li>';
    }

    $return_string .= '</ul>';

    wp_reset_query();
    return $return_string;

}
add_shortcode( 'pr-archive', 'pr_archive' );

// filter used on upcoming events template
function pva_events_where( $where ) {
    
    $where = str_replace("meta_key = 'event_dates_times_$", "meta_key LIKE 'event_dates_times_%", $where);

    return $where;
}

add_filter('posts_where', 'pva_events_where');

/**
 * Give Form Require Fields
 *
 * @description: Cusomize the prefix of this function before using. The $required_fields array key is the name of the field you would like to make required; ie " name='card_address_2' " - find using Debug Tools, etc.
 *
 * @param $required_fields
 *
 * @return mixed
 */
function pva_give_form_required_fields( $required_fields ) {

    //First Name
    $required_fields['give_first'] = array(
        'error_id' => 'invalid_last_name',
        'error_message' => __( 'Please enter your first name.', 'give' )
    );

    //Last Name
    $required_fields['give_last'] = array(
        'error_id' => 'invalid_last_name',
        'error_message' => __( 'Please enter your last name.', 'give' )
    );

        //Uncomment to add Address
   $required_fields['card_address'] = array(
       'error_id' => 'invalid_card_address',
       'error_message' => __( 'Please enter your address.', 'give' )
   );

      //ACustom Field
//    $required_fields['my_custom_field'] = array(
//        'error_id' => 'invalid_my_custom_field',
//        'error_message' => __( 'Custom field required message.', 'give' )
//    );

    return $required_fields;
}
add_filter( 'give_purchase_form_required_fields', 'pva_give_form_required_fields' );