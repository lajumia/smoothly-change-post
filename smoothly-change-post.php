<?php
/**
 * Plugin Name: Smoothly Change Post
 * Description: Registers a custom post type 'properties' with full WordPress post features (title, editor, featured image, categories, tags, etc.).
 * Version: 1.0
 * Author: Laju
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue style in single property post page only
add_action('wp_enqueue_scripts', 'spc_enqueue_single_property_css');
function spc_enqueue_single_property_css() {
    if (is_singular('properties')) {
        wp_enqueue_style(
            'spc-single-property-style',
            plugin_dir_url(__FILE__) . 'assets/css/single-property.css',
            array(),
            '1.0.0'
        );
    }
}

// Enqueue script for silk slider
function enqueue_slick_slider_assets() {
    wp_enqueue_script('slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), null, true);
    wp_enqueue_style('slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css');
    wp_enqueue_style('slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');


// Register Custom Post Type
function scp_register_properties_post_type() {
    $labels = array(
        'name'               => 'Properties',
        'singular_name'      => 'Property',
        'menu_name'          => 'Properties',
        'name_admin_bar'     => 'Property',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Property',
        'new_item'           => 'New Property',
        'edit_item'          => 'Edit Property',
        'view_item'          => 'View Property',
        'all_items'          => 'All Properties',
        'search_items'       => 'Search Properties',
        'not_found'          => 'No properties found.',
        'not_found_in_trash' => 'No properties found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'menu_icon'          => 'dashicons-admin-home',
        'menu_position'      => 5,
        'rewrite'            => array( 'slug' => 'properties' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields' ),
        'taxonomies'         => array( 'property_category', 'property_tag' ), // custom taxonomies
    );

    register_post_type( 'properties', $args );
}
add_action( 'init', 'scp_register_properties_post_type' );

// Register custom taxonomy: Categories
function scp_register_property_category_taxonomy() {
    $labels = array(
        'name'              => 'Property Categories',
        'singular_name'     => 'Property Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'parent_item'       => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Categories',
    );

    register_taxonomy( 'property_category', 'properties', array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'property-category' ),
    ) );
}
add_action( 'init', 'scp_register_property_category_taxonomy' );

// Register custom taxonomy: Tags
function scp_register_property_tag_taxonomy() {
    $labels = array(
        'name'                       => 'Property Tags',
        'singular_name'              => 'Property Tag',
        'search_items'               => 'Search Tags',
        'popular_items'              => 'Popular Tags',
        'all_items'                  => 'All Tags',
        'edit_item'                  => 'Edit Tag',
        'update_item'                => 'Update Tag',
        'add_new_item'               => 'Add New Tag',
        'new_item_name'              => 'New Tag Name',
        'separate_items_with_commas' => 'Separate tags with commas',
        'add_or_remove_items'        => 'Add or remove tags',
        'choose_from_most_used'      => 'Choose from the most used tags',
        'menu_name'                  => 'Tags',
    );

    register_taxonomy( 'property_tag', 'properties', array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rewrite'               => array( 'slug' => 'property-tag' ),
    ) );
}
add_action( 'init', 'scp_register_property_tag_taxonomy' );

// Load custom template to property
function smoothly_load_custom_single_template($template) {
    if (is_singular('properties')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-properties.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'smoothly_load_custom_single_template');

// Fetch custom post meta
add_action('wp_ajax_fetch_custom_post_data', 'fetch_custom_post_data_callback');
add_action('wp_ajax_nopriv_fetch_custom_post_data', 'fetch_custom_post_data_callback');

function fetch_custom_post_data_callback() {
    $post_id = intval($_POST['post_id']);

    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
    }

    $current_post = get_post($post_id);
    if (!$current_post) {
        wp_send_json_error('Post not found');
    }

    $post_type = $current_post->post_type;

    // Change to your custom taxonomy
    $taxonomy = 'property_category';

    // Get the first term (adjust if needed)
    $terms = wp_get_post_terms($post_id, $taxonomy);
    if (empty($terms)) {
        wp_send_json_error('No taxonomy terms found');
    }

    $term_id = $terms[0]->term_id;

    // Fetch all post IDs in this term ordered by menu_order
    $args = [
        'posts_per_page' => -1,
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $term_id,
            ],
        ],
        'suppress_filters' => false,
        'fields' => 'ids',
    ];

    $post_ids = get_posts($args);
    $current_index = array_search($post_id, $post_ids);
    $total = count($post_ids);

    // Circular navigation
    $prev_post_id = $post_ids[($current_index - 1 + $total) % $total];
    $next_post_id = $post_ids[($current_index + 1) % $total];

    // Now fetch custom fields (same as before)
    $first_field = get_post_meta($post_id, 'first_paragraph', true);
    $second_field = get_post_meta($post_id, 'second_paragraph', true);
    $third_image_field_url = wp_get_attachment_image_url(get_post_meta($post_id, 'third_image_element', true), 'large');
    $fourth_first_field = get_post_meta($post_id, 'fourth_first_paragraph', true);
    $fourth_second_field = get_post_meta($post_id, 'fourth_second_paragraph', true);
    $fifth_image_id = get_post_meta($post_id, 'fifth_first_image', true);
    $fifth_image_filed_url = $fifth_image_id ? wp_get_attachment_image_url($fifth_image_id, 'large') : '';
    $fifth_text_field = get_post_meta($post_id, 'fifth_paragraph', true);
    $gallery_ids = get_post_meta($post_id, 'silk_slider_images', true);
    $background_image = wp_get_attachment_image_url(get_post_meta($post_id, 'background_image', true), 'large');

    $gallery_urls = [];
    if (!empty($gallery_ids) && is_array($gallery_ids)) {
        foreach ($gallery_ids as $id) {
            $gallery_urls[] = wp_get_attachment_image_url($id, 'large');
        }
    }

    wp_send_json_success([
        'first_text'         => $first_field,
        'second_text'        => $second_field,
        'third_image'        => $third_image_field_url,
        'fourth_first_text'  => $fourth_first_field,
        'fourth_second_text' => $fourth_second_field,
        'fifth_image'        => $fifth_image_filed_url,
        'fifth_text'         => $fifth_text_field,
        'gellery'            => $gallery_urls,
        'container_background' => $background_image,
        'prev_post_id'       => $prev_post_id,
        'next_post_id'       => $next_post_id,
        'permalink'          => get_permalink($post_id),
        'next_post_url'=> get_permalink($next_post_id),
        'prev_post_url'=> get_permalink($prev_post_id),
    ]);
}


wp_enqueue_script('custom-ajax-script', plugin_dir_url(__FILE__) . 'assets/js/custom.js', ['jquery'], null, true);

wp_localize_script('custom-ajax-script', 'ajax_object', [
    'ajax_url' => admin_url('admin-ajax.php'),
]);

