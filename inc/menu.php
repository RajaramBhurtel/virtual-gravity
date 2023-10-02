<?php 

function register_food_category_post_type() {
    $labels = array(
        'name'                  => _x( 'Food Categories', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Food Category', 'Post Type Singular Name', 'text_domain' ),
        'all_items'             => __( 'All Food Categories', 'text_domain' ),
        'add_new_item'          => __( 'Add New Food Category', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Food Category', 'text_domain' ),
        'description'           => __( 'Food Category Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-food',
        'show_in_admin_bar'     => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'food_category', $args );
}
add_action( 'init', 'register_food_category_post_type', 0 );


add_action( 'init', 'register_food_category_post_type', 0 );

// Add custom meta box for Food List
function add_food_list_meta_box() {
    add_meta_box(
        'food_list_meta_box',
        'Food List',
        'render_food_list_meta_box',
        'food_category', // Post type
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_food_list_meta_box' );


// Render custom meta box for Food List
function render_food_list_meta_box( $post ) {
    $food_list = get_post_meta( $post->ID, '_food_list', true );

    ?>
    <label for="food_list">Food List:</label>
    <div id="food-list-items">
        <?php
        if (!empty($food_list)) {
            $food_items = explode("\n", $food_list);
            foreach ($food_items as $item) {
                echo '<input type="text" name="food_item[]" value="' . esc_attr($item) . '"><br>';
            }
        }
        ?>
    </div>
    <button type="button" id="add-food-item">Add Food Item</button>
    <script>
        jQuery(document).ready(function($) {
            $('#add-food-item').click(function() {
                $('#food-list-items').append('<input type="text" name="food_item[]"><br>');
            });
        });
    </script>
    <?php
}


// Save Food List field data
function save_food_list_field( $post_id ) {
    if ( isset( $_POST['food_item'] ) ) {
        $food_items = $_POST['food_item'];
        $food_list = implode("\n", array_map('sanitize_text_field', $food_items));
        update_post_meta( $post_id, '_food_list', $food_list );
    }
}
add_action( 'save_post_food_category', 'save_food_list_field' );

function food_category_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'per_page' => -1, // Default to display all
    ), $atts);

    $food_category_query = new WP_Query(array(
        'post_type' => 'food_category',
        'posts_per_page' => intval($atts['per_page']), 
    ));

    $output = '<div class="food-categories-container">';

    if ($food_category_query->have_posts()) {
        while ($food_category_query->have_posts()) {
            $food_category_query->the_post();

            $food_name = get_the_title();
            $food_description = get_the_content();
            $food_image = get_the_post_thumbnail();
            $food_list = get_post_meta(get_the_ID(), '_food_list', true);

            $output .= '<div class="food-category">';
            $output .= '<h2>' . esc_html($food_name) . '</h2>';
            if ($food_image) {
                $output .= '<div class="food-image">' . $food_image . '</div>';
            }
            if ($food_description) {
                $output .= '<p class="food-description">' . ($food_description) . '</p>';
            }
            if ($food_list) {
                $output .= '<ul class="food-list"><b>Food List</b>';
                $food_items = explode("\n", $food_list);
                foreach ($food_items as $item) {
                    $output .= '<li>' . esc_html($item) . '</li>';
                }
                $output .= '</ul>';
            }
            $output .= '</div>';
        }

        wp_reset_postdata();
    }

    $output .= '</div>'; // Close the container

    return $output;
}
add_shortcode('food_category', 'food_category_shortcode');


