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
        'public' => false, 
        'show_ui' => true,
        'menu_icon' => 'dashicons-email', 
        'supports' => array('title', 'editor'),
    );

    register_post_type('contact_form', $args);
}
add_action('init', 'create_custom_contact_form_post_type');


function custom_contact_form() {
    ob_start();
    ?>
    <form id="custom-contact-form" method="post">
        <label for="name"><?php echo esc_html__( 'Name:', 'virtual-gravity' ); ?></label>
        <input type="text" name="name" id="name" required><br>

        <label for="email"><?php echo esc_html__( 'Email:', 'virtual-gravity' ); ?></label>
        <input type="email" name="email" id="email" required><br>

        <?php do_action('custom_contact_form_custom_fields'); ?>
        <label for="message"><?php echo esc_html__( 'Message:', 'virtual-gravity' ); ?></label>
        <textarea name="message" id="message" rows="4" required></textarea><br>

        <input type="hidden" name="action" value="custom_contact_form_submit">
        <input type="submit" value="Submit">
        <span id="result" class="loading"></span>
    </form>
    <?php
    return ob_get_clean();
}

add_action('wp_ajax_custom_contact_form_submit', 'custom_contact_form_handle_ajax_submission');
add_action('wp_ajax_nopriv_custom_contact_form_submit', 'custom_contact_form_handle_ajax_submission');

function custom_contact_form_handle_ajax_submission() {
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {

        
        $custom_data = '';

        // Name should be followed as : custom_field_
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'custom_data_') === 0) {
                $field_name = sanitize_text_field($key);
                $field_value = sanitize_text_field($value);
                $custom_data .= "$field_name: $field_value\n";
            }
        }

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_text_field($_POST['message']);
        $post_content = "Email: $email\nMessage: $message\n$custom_data";

        $post_data = array(
            'post_title' => $name,
            'post_content' => $post_content,
            'post_type' => 'contact_form',
            'post_status' => 'publish',
            'meta_input'  => [
                        '_contact_email' => $email
                    ]
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {

            wp_send_json_success(array('message' => 'Form submitted successfully'));
        } else {
            wp_send_json_error(array('message' => 'Error occurred while saving the form'));
        }
    } else {
        wp_send_json_error(array('message' => 'Invalid form data'));
    }

    exit();
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

add_shortcode('custom_contact_form', 'custom_contact_form');

// Hook to add custom fields to the form
add_action('custom_contact_form_custom_fields', 'add_custom_fields');

function add_custom_fields() {

    $field_name = 'custom_data_' . uniqid(); 
    ?>
    <label for="<?php echo $field_name; ?>">Custom Field:</label>
    <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>"><br>
    <?php
}

