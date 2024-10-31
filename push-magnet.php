<?php
if( ! defined('ABSPATH') ) exit;
/**
 * Plugin Name: Push Magnet Web Push Notifications
 * Description: The ultimate tool to effortlessly engage and retain your website visitors. With seamless integration, this plugin empowers you to send targeted notifications directly to your users' browsers, keeping them informed, engaged, and coming back for more. Whether it's promoting new content, announcing sales, or delivering important updates, harness the power of web push notifications to drive traffic and conversions like never before. Easy to set up and customize, our plugin is the missing piece to complete your website's engagement strategy.
 * Version: 1.0.0
 * Author: InfoTalks
 * Author URI: https://infotalks.in/
 * License: GPLv3
 */

class PushMagnet {
    protected function __construct(){
		if( get_option('pushmagnet_key') && get_option('pushmagnet_public_key'))
            define('PUSHMAGNET',TRUE);

        include_once('include/push-magnet_functions.php');

        //add menu in the menu bar
        add_action('admin_menu', 'pushMagnet_menu');

        //add css file for admin
		add_action( 'admin_enqueue_scripts', 'pushmagnet_push_admin_css' );

        register_activation_hook( __FILE__, 'pushMagnet_setup' );
        //add script in head
        add_action ( 'admin_init', 'pushmagnet_save_settings' );

        add_action( 'transition_post_status', 'pushmagnet_post_published_send_notification', 10, 3 );

        if(defined('PUSHMAGNET')){
            add_action( 'wp_footer', 'pushmagnet_insert_script');
        }
    }

    public static function init(){
        static $instance = null;

        if(! $instance){
            $instance = new PushMagnet();
        }
         return $instance;
    }
}

PushMagnet::init();