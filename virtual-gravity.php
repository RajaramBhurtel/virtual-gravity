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

    add_action( 'plugins_loaded', 'virtual_gravity_load' ) ;
	add_action( 'wp_enqueue_scripts', 'load_stylesheets' );


    function virtual_gravity_load(){
        define( 'VG_File', __FILE__ );
        define( 'VG_Url', plugin_dir_url( __FILE__ ) );
        define( 'VG_Path', plugin_dir_path( VG_File ) );

        $files = array(
            'inc/contact-form.php',
            'inc/menu.php',
        );

        $files = array_map(function( $file ){
            return VG_Path . $file;
        }, $files);

        foreach( $files as $file ){
            require $file;
        }

    }

    /* adds stylesheet file to the end of the queue */
    function load_stylesheets()
		{
		    $dir = plugin_dir_url(__FILE__);
		    wp_enqueue_style( 'VGStyle', $dir . '/assets/style.css', array(), '0.1.0', 'all' );

            wp_enqueue_script( 'VGScript', $dir . '/assets/scripts.js', array( 'jquery' ), '1.0.0', true );

            wp_localize_script( 'VGScript', 'VG', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                )
            );
		}
