<?php
/**
 * Plugin Name: Sujan Permalink Refresher
 * Plugin URI: https://mp3giant.com
 * Description: A custom plugin for  refreshing wordpress permalink structure
 * Version: 1.0.0
 * Author: Md Toriqul Mowla Sujan
 * Author URI: https://mtmsujan.com
 * Text Domain: sujan
 * Domain Path: /languages
 */

// prevent direct access to the file
defined('ABSPATH') || die('No direct script access allowed!');


add_action('init', function(){
    
    flush_rewrite_rules();
});

// ADDING CUSTOM INTERVAL 

add_filter('cron_schedules','sujan_add_cron_interval');
function sujan_add_cron_interval($schedules){
    
    $schedules["ten_seconds"] = array(
        'interval' => 10,
        'display' => __('Every 10 seconds'));

    return $schedules;
}


// SETTING MY CUSTOM HOOK FOR WP CRON 

add_action('sujan_cron_hook', 'sujan_cron_test');

// THE EVENT FUNCTION 

function sujan_cron_test(){

    // $random_val = rand(1, 100000);
    // $permalink_structure = '/' . $random_val . '/%post_id%/%postname%/';
    // update_option('permalink_structure', $permalink_structure, true);

    // rewrites custom post type name
    global $wp_rewrite; 

    //Write the rule
    $random_val = rand(1, 100000);
    $wp_rewrite->set_permalink_structure('/' . $random_val . '/%post_id%/%postname%/'); 

    //Set the option
    update_option( "rewrite_rules", FALSE ); 

    //Flush the rules and tell it to write htaccess
    $wp_rewrite->flush_rules( true );
    
}


// TO PREVENT DUPLICATE EVENTS 

register_activation_hook( __FILE__, 'sujan_activation' );

function sujan_activation() {
    if( ! wp_next_scheduled( 'sujan_cron_hook' ) ){
        // SCHEDUELING RECURRING EVENT 
        wp_schedule_event( time(), 'ten_seconds', 'sujan_cron_hook' );

    }
}


register_deactivation_hook( __FILE__, 'my_deactivation' );
 
function my_deactivation() {
    wp_clear_scheduled_hook( 'sujan_cron_hook' );
}
