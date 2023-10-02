<?php
/**
 * Plugin Name: Virtual Gravity
 * Description: 
 * Author: Raja Ram Bhurtel
 * Author URI: www.rajarambhurtel.com.np
 * Version: 1.0
 * Text Domain: virtual-gravity
 */

# Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Class
 */

class Virtual_Gravity{

    /**
     * Default Construct
     */
    public function __construct() {
        // add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        add_action( 'plugins_loaded', array( $this, 'virtual_gravity_load' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_stylesheets' ));

    }

    /**
     * Function to laod on plugin loads
     *
     * @return admin notice to activate WooCommerce
     */
    public function on_plugins_loaded() {
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
           add_action( 'admin_notices', array( $this, 'admin_notice_need_woocommerce' ));
        }
    }

    /**
     * Function to add admin notice
     *
     * @return admin notice to activate WooCommerce
     */
    public function admin_notice_need_woocommerce() {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . esc_html__( 'Virtual_Gravity requires WooCommerce to be active to work', 'virtual_gravity' ) . '</p>';
        echo '</div>';
    }

    /**
     * Main function of the plugin
     *
     */
    public function virtual_gravity_load(){
        define( 'VG_File', __FILE__ );
        define( 'VG_Url', plugin_dir_url( __FILE__ ) );
        define( 'VG_Path', plugin_dir_path( VG_File ) );

        $files = array(
            'inc/contact-form.php',
        );

        $files = array_map(function( $file ){
            return VG_Path . $file;
        }, $files);

        foreach( $files as $file ){
            require $file;
        }

    }

    /* adds stylesheet file to the end of the queue */
	public function load_stylesheets()
		{
		    $dir = plugin_dir_url(__FILE__);
		    wp_enqueue_style( 'VGStyle', $dir . '/assets/style.css', array(), '0.1.0', 'all' );

            wp_enqueue_script( 'VGScript', $dir . '/assets/scripts.js', array( 'jquery' ), '1.0.0', true );

            wp_localize_script( 'VGScript', 'VG', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
            )
        );
		}

}

new Virtual_Gravity();