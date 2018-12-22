<?php
/*
Plugin Name: AJAX Contact Widget
Description: Simple AJAX powered contact form widget
Version: 0.1.0
Author: Vladimir Kamuz
Author URI: https://wpdev.pp.ua
License: GPL2
*/

/**
 * Include JavaScript
 */
function add_scripts(){
    wp_enqueue_script('contact-scripts', plugins_url(). '/contact-widget/js/script.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('contact-style', plugins_url(). '/contact-widget/css/style.css', null, '1.0.0');
}
add_action('wp_enqueue_scripts', 'add_scripts');

/**
 * Include Class
 */
include('class.contact-widget.php');

/**
 * Register Widget
 */
function register_contact_widget(){
    register_widget('Contact_Widget');
}
add_action('widgets_init', 'register_contact_widget');