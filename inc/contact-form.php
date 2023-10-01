<?php 

function create_custom_contact_form_post_type() {
    $labels = array(
        'name' => 'Contact Forms',
        'singular_name' => 'Contact Form',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Contact Form',
        'edit_item' => 'Edit Contact Form',
        'new_item' => 'New Contact Form',
        'view_item' => 'View Contact Form',
        'view_items' => 'View Contact Forms',
        'search_items' => 'Search Contact Forms',
        'not_found' => 'No Contact Forms found',
        'not_found_in_trash' => 'No Contact Forms found in trash',
        'all_items' => 'All Contact Forms',
    );

    $args = array(
        'labels' => $labels,
        'public' => false, // Set to false if you don't want public access
        'show_ui' => true,
        'menu_icon' => 'dashicons-email', // Icon for the menu item in the admin menu
        'supports' => array('title', 'editor'),
    );

    register_post_type('contact_form', $args);
}
add_action('init', 'create_custom_contact_form_post_type');


function custom_contact_form() {
    ob_start();
    ?>
    <form id="custom-contact-form" method="post" action="">
        <?php do_action('custom_contact_form_before_name_field'); ?>
        <label for="name"><?php echo esc_html__( 'Name:', 'virtual-gravity' ); ?></label>
        <input type="text" name="name" id="name" required><br>

        <?php do_action('custom_contact_form_before_email_field'); ?>
        <label for="email"><?php echo esc_html__( 'Email:', 'virtual-gravity' ); ?></label>
        <input type="email" name="email" id="email" required><br>

        <?php do_action('custom_contact_form_before_message_field'); ?>
        <label for="message"><?php echo esc_html__( 'Message:', 'virtual-gravity' ); ?></label>
        <textarea name="message" id="message" rows="4" required></textarea><br>

        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean();
}

function handle_contact_form_submission() {
	error_log('Form submission function executed.'); 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_post'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_text_field($_POST['message']);
      
        // Create a new post of the "Contact Forms" custom post type
        $post_data = array(
            'post_title' => $name,
            'post_content' => "Email: $email\nMessage: $message",
            'post_type' => 'contact_form',
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post($post_data);

    }
}

function add_custom_columns_to_contact_forms($columns) {
    $columns['name'] = 'Name';
    $columns['email'] = 'Email';
    return $columns;
}
add_filter('manage_contact_form_posts_columns', 'add_custom_columns_to_contact_forms');

function display_custom_columns_for_contact_forms($column, $post_id) {
    switch ($column) {
        case 'name':
            echo get_the_title($post_id);
            break;
        case 'email':
            $email = get_post_meta($post_id, '_contact_email', true);
            echo esc_html($email);
            break;
    }
}
add_action('manage_contact_form_posts_custom_column', 'display_custom_columns_for_contact_forms', 10, 2);

// Shortcode to display the contact form
add_shortcode('custom_contact_form', 'custom_contact_form');

// Hook to handle form submissions
add_action('init', 'handle_contact_form_submission');


