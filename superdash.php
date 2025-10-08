<?php
/**
 * Plugin Name: Super Dash
 * Description: Une page admin pour les test
 * Version: 1.1.0
 * Author: Abdelkarim
 */

add_action('admin_menu', 'superdash_admin_page');

function superdash_admin_page()
{
    // MAIN PAGE
    add_menu_page(
        'Super Dashboard',
        'HQ Dash',
        'manage_options',
        'hq-dashboard',
        'hq_simple_admin_page',
        'dashicons-smiley',
        30 
    );
    add_submenu_page(
        'hq-dashboard',
        'Settings Page',
        'Settings',
        'manage_options',
        'hq-settings',
        'hq_settings_page' 
    );
    add_submenu_page(
        'hq-dashboard',   
        'Reports Page',        
        'Reports', 
        'manage_options',        
        'hq-reports',            
        'hq_reports_page'         
    );
    remove_submenu_page('hq-dashboard', 'hq-dashboard');
}


function hq_simple_admin_page()
{
    ?>
    <div class="wrap">
        <h1>HQ Dashboard</h1>
        <p>Welcome to your main dashboard page.</p>
    </div>
    <?php
}


function hq_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Settings</h1>
        <p>Configure your plugin options here.</p>
    </div>
    <?php
}

function hq_reports_page()
{
    ?>
    <div class="wrap">
        <h1>Reports</h1>
        <p>View your reports and analytics here.</p>
    </div>
    <?php
}

add_shortcode( 'salam', 'say_hi');
function say_hi($atts){
    $atts = shortcode_atts( array(
        'name'=>'Kubra',
        'emoji' => '😜'
    ), $atts, 'salam' );  

    return "<div>Hello <strong>{$atts['name']}</strong> <strong>{$atts['emoji']}</strong></div>";
}


function setting_home_page(){
    ?>
    <div class="wrap">

    </div>
    <?php
}


?>