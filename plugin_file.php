<?php
/**
* Plugin Name: Test Plugin
* Plugin URI: https://www.localhost.com/
* Description: This plugin will stop all the updates.
* Version: 0.1
* Author: Shavet
* Author URI: https://www.localhost.com/
**/
class Disable_All_Updates{
    function __construct(){
        add_action('admin_init',array(&$this,'admin_init'));
        add_action('login_head',array(&$this,'custom_login_logo'));
        add_filter('login_headerurl',array(&$this,'custom_login_logo_url'));
        add_action('admin_head',array(&$this,'custom_admin_bar_logo')); // for the back-end
        add_action('wp_head',array(&$this,'custom_admin_bar_logo')); // for the front end
        add_action('admin_footer',array(&$this,'custom_admin_bar_logo_link')); //Trigger on backend
        add_action('wp_footer',array(&$this,'custom_admin_bar_logo_link')); //Trigger on front-end
        add_action('admin_head',array(&$this,'custom_remove_wp_links_under_the_logo')); // Hide on backend
        add_action('wp_head',array(&$this,'custom_remove_wp_links_under_the_logo')); // Hide on frontend
        add_action('admin_enqueue_scripts', array(&$this,'ds_admin_theme_style'));
        add_action('login_enqueue_scripts', array(&$this,'ds_admin_theme_style'));
        add_action('admin_menu', array(&$this,'hide_plugin_update_indicator'));
    }

    function hide_plugin_update_indicator(){
        global $menu,$submenu;
        $menu[65][0] = 'Plugins';
        $submenu['index.php'][10][0] = 'Updates';
    }

   
    function ds_admin_theme_style() {
        if (!current_user_can( 'manage_options' )) {
        echo '<style>.update-nag, .updated, .error, .is-dismissible { display: none; }</style>';}}
    function admin_init() {
        
        if (!current_user_can('update_core')) {
            return;
        }

        if ( !function_exists("remove_all_actions") ) return;
        
        global $current_user;
        $current_user->allcaps['update_plugins'] = 0;

        remove_all_actions( 'admin_notices');
        remove_all_actions( 'network_admin_notices');
        remove_all_actions( 'user_admin_notices');

        add_filter('pre_site_transient_update_core', '__return_null');
        add_filter('pre_site_transient_update_plugins', '__return_null');
        add_filter('pre_site_transient_update_themes', '__return_null');
    }

    function custom_login_logo() {
        echo '<style type="text/css">
			h1 a { background-image: url("'.plugin_dir_url(__FILE__).'/assets/images/logo-opt.png") !important; }
		</style>';
	}

    function custom_login_logo_url() {
        global $wp;
        return home_url( $wp->request );
    }

    function custom_admin_bar_logo() {
        if(!is_user_logged_in()){
            return;
        }
        echo '
        <style>
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                background-image: url("'.plugin_dir_url(__FILE__).'/assets/images/logo2.png") !important;
                background-position: 0 0;
                color:rgba(0, 0, 0, 0);
                background-size: 20px 20px;
                background-repeat: no-repeat;
            }
            #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon {
                background-position: 0 0;
            }
            #wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon:before{
                top:7px;
        </style>
        ';
    }

    function custom_admin_bar_logo_link() {
        global $wp;
        if( !is_user_logged_in() ){
            return;
        }
        echo "
        <script type='text/javascript'>
            (function(){
                document
                    .getElementById('wp-admin-bar-wp-logo')
                    .children[0]
                    .setAttribute('href', '".admin_url('about.php')."')
            })();
        </script>
        ";
    }
        function custom_remove_wp_links_under_the_logo() {
            if( !is_user_logged_in() ){
                return;
            }
            echo '
            <style>
                #wpadminbar .ab-top-menu>.menupop>.ab-sub-wrapper{
                    display: none !important;
                }
            </style>
            ';
        }
        
}

if ( class_exists('Disable_All_Updates') ) {
	$OS_Disable_WordPress_Updates = new Disable_All_Updates();
}