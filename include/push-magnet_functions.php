<?php if( ! defined('ABSPATH') ) exit;
function pushmagnet_push_admin_css(){
    wp_enqueue_style('push-css', plugins_url('css/push-magnet_admin.css', __DIR__), '', '1.0.0', false);
}

function pushMagnet_menu(){
    if(defined('PUSHMAGNET')){
        add_menu_page('Push Magnet - Dashboard', 'Push Magnet', 'edit_dashboard', 'push-magnet_dashboard', 'pushMagnet_dashboard_page', 'dashicons-megaphone');
        add_submenu_page('push-magnet_dashboard', 'Push Magnet - Dashboard', 'Dashboard', 'edit_dashboard', 'push-magnet_dashboard', 'pushMagnet_dashboard_page');
        add_submenu_page('push-magnet_dashboard', 'Push Magnet - Configuration', 'Configuration', 'manage_options', 'push-magnet_configuration', 'pushMagnet_configuration_page');
        // add_submenu_page('push-magnet_dashboard', 'Push Magnet - Insights', 'Insights', 'manage_options', 'push-magnet_insights', 'pushMagnet_insights_page');
        add_submenu_page('push-magnet_dashboard', 'Push Magnet - Setup', 'Setup', 'manage_options', 'push-magnet_setup', 'pushMagnet_setup_page');
    } else {
        add_menu_page('Push Magnet - Setup', 'Push Magnet', 'manage_options', 'push-magnet_setup', 'pushMagnet_setup_page', 'dashicons-megaphone');
        add_submenu_page('push-magnet_setup', 'Push Magnet - Setup', 'Setup', 'manage_options', 'push-magnet_setup', 'pushMagnet_setup_page');
    }
}

function pushMagnet_dashboard_page(){
    wp_enqueue_script('push-magnet-chart', plugins_url('js/Chart.min.js', __DIR__), '', '4.4.3', false);
    include_once('push-magnet_dashboard.php');
}

function pushMagnet_configuration_page(){
    include_once('push-magnet_configuration.php');
}

function pushMagnet_insights_page(){
    include_once('push-magnet_insights.php');
}

function pushMagnet_setup_page(){
    include_once('push-magnet_setup.php');
}

function pushmagnet_insert_script(){
    $pushmagnet_integration = get_option('pushmagnet_disable_prompt_code');
    if(!$pushmagnet_integration){ ?>
        <script id="pushmagnet-script">
        (function(w,d,e,id){w.pushmagnet=w.pushmagnet||function(){(w.pushmagnet.q=w.pushmagnet.q||[]).push(arguments)};var js=d.createElement(e);js.async=1;js.id=id;js.src="https://app.pushmagnet.com/v1/app.min.js";d.body.appendChild(js);})(window, document, 'script', 'pushmagnet-sdk');
        pushmagnet({'key': '<?php echo esc_html(get_option( 'pushmagnet_public_key' )); ?>', 'sw': '<?php echo esc_html(plugins_url('js/pushmagnet-sw.js.php',dirname(__FILE__)));?>'})
        </script>
    <?php }
}

function pushMagnet_setup(){
	add_option('pushmagnet_do_activation_redirect', true);
}
function pushmagnet_api_request($api_end_point, $api_payload_data = null){
    $request['headers'] = array('Content-Type' => 'Application/Json', 'pushmagnetpublickey' => get_option('pushmagnet_public_key'), 'pushmagnetprivatekey' => get_option('pushmagnet_key'));
    if($api_payload_data != null){
        $request['body'] = wp_json_encode($api_payload_data);
    }
    $result = wp_remote_post($api_end_point, $request);
    if(!is_wp_error($result)){
        $http_code = $result['headers']['code'];
        $response = wp_remote_retrieve_body($result);
        $res_array = json_decode($response, true);
        return array('http_code' => $http_code, 'response_json' => $response, 'response_array' => $res_array);
    } else
        return array('http_code' => 404, 'response_json' =>'', 'response_array' => array(), 'message' => $result->get_error_message());
}

function pushmagnet_post_published_send_notification($new_status, $old_status, $post){
    if(!$post)
        return;

    if($new_status != 'publish')
        return

    $ID = $post->ID;

    if(get_option('pushmagnet_enable_post') == 'on' && in_array($post->post_type, json_decode(get_option('pushmagnet_post_type'))))
    {
        if(get_option('pushmagnet_post_icon') == '{featured_image}')
            $pmNotificationIcon = get_the_post_thumbnail_url($ID, array(256, 256));
        else
            $pmNotificationIcon = ge_option('pushmagnet_post_icon');

        if(get_option('pushmagnet_post_image') == '{featured_image}')
            $pmNotificationImage = get_the_post_thumbnail_url($ID, array(512, 512));
        else
            $pmNotificationImage = get_option('pushmagnet_post_image');
        $pmNotificationTitle = $post->post_title;
        $pmNotificationBody = get_the_excerpt($post->ID) ?: strip_shortcodes($post->post_content);
        $pmNotificationURI = get_permalink($ID);
        $end_point = 'https://app.pushmagnet.com/notification/send';
        $request['headers'] = array('Content-Type' => 'Application/Json');
        $req_data = array('title' => $pmNotificationTitle, 'message' => substr($pmNotificationBody, 0, 300),
                            'target_url' => $pmNotificationURI, 'icon' => $pmNotificationIcon, 'image' => $pmNotificationImage, 'site' => get_option('siteurl'));
        $request['body'] = wp_json_encode($req_data);
        $result = wp_remote_post($end_point, $request);

        if(!is_wp_error($result)){
            //$response = wp_remote_retrieve_body($result);
        }
    }
    

}

function pushmagnet_save_settings(){
    if(isset($_POST['frm_pushmagnet_setup'])){
        if(!isset( $_POST['pushmagnet_setup_nonce_field'] ) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pushmagnet_setup_nonce_field'])), 'pushmagnet_setup_nounce_action')){
            exit();
        }

        if( !current_user_can('manage_options')){
            add_action('admin_notice', 'pushmagnet_authorization_failed');
            return;
        }

        if($_POST['push-magnet_api-key']){
            $request['headers'] = array('Content-Type' => 'Application/Json', "key" => sanitize_text_field(stripslashes($_POST['push-magnet_api-key'])));
            $request['body'] = wp_json_encode(array('site_url' => site_url()));
            $result = wp_remote_post('https://app.pushmagnet.com/v1/authentication/wordpress', $request);
            if( !is_wp_error($result)){
                $response = wp_remote_retrieve_body($result);
                $res_array = json_decode($response, true);
                $http_code = $result['response']['code'];
                if($http_code == 200){
                    update_option('pushmagnet_key', sanitize_text_field(stripslashes($_POST['push-magnet_api-key'])));
                    update_option('pushmagnet_public_key', $res_array['public_key']);
                    add_option('pushmagnet_enable', 'on');
                    add_option('pushmagnet_enable_post','on');
					add_option('pushmagnet_enable_post_update','on');
                    add_option('pushmagnet_post_message','{post_excerpt}');
					add_option('pushmagnet_post_title','{post_title}');
					add_option('pushmagnet_post_type','["post"]');
                    add_option('pushmagnet_post_image',"{featured_image}");
					add_option('pushmagnet_post_icon',"{featured_image}");
                    add_option('pushmagnet_post_segments', '["all"]');
                    header('Location: admin.php?page=push-magnet_configuration');
					exit();	
                }else{
					add_action( 'admin_notices', 'pushmagnet_settings_failed' );
				}
            }
        }
    } else if(isset($_POST['pushmagnet_save_settings'])){
        if(!isset($_POST['pushmagnet_configuration_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pushmagnet_configuration_nonce_field'])), 'pushmagnet_configuration_nounce_action')){
            exit();
        }
        if (!current_user_can('manage_options')){	
			add_action( 'admin_notices', 'pushmagnet_authorization_failed' );
			return;
		}

        update_option('pushmagnet_enable', isset($_POST['pushmagnet_enable']) == 'on' ? 'on' : 'off');
        if(isset($_POST['pushmagnet_enable']) && $_POST['pushmagnet_enable'] == 'on'){
            update_option('pushmagnet_post_title', sanitize_text_field(stripslashes($_POST['pushmagnet_post_title'])));
			update_option('pushmagnet_post_message', sanitize_text_field(stripslashes($_POST['pushmagnet_post_message'])));
			update_option('pushmagnet_utm_parameter', sanitize_text_field(stripslashes($_POST['pushmagnet_utm_parameter'])));
			update_option('pushmagnet_post_icon', sanitize_text_field(stripslashes($_POST['pushmagnet_post_icon'])));
			update_option('pushmagnet_post_segments', json_encode(stripslashes(sanitize_text_field($_POST['pushmagnet_post_segments']))));
			update_option('pushmagnet_post_image', sanitize_text_field(stripslashes($_POST['pushmagnet_post_image'])));
			update_option('pushmagnet_post_type', json_encode(stripslashes(sanitize_text_field($_POST['wpp_post_type']))));
			update_option('pushmagnet_enable_post', isset($_POST['pushmagnet_enable_post']) == 'on' ? 'on' : 'off'); 
			update_option('pushmagnet_enable_post_update', isset($_POST['pushmagnet_enable_post_update']) == 'on' ? 'on' : 'off');
			add_action('admin_notices', 'pushmagnet_settings_saved');
        }
    } else if(get_option('pushmagnet_do_activation_redirect', false)){
        delete_option('pushmagnet_do_activation_redirect');
        wp_redirect( esc_url(admin_url( 'admin.php?page=push-magnet_setup' )) );
        exit;
    }
}
function pushmagnet_settings_saved(){
    ?>
  <div class="notice notice-success is-dismissible">
      <p><?php esc_html_e( 'Settings have been saved successfully!' , 'push-magnet-web-push-notifications'); ?></p>
  </div>
  <?php
}
function pushmagnet_settings_failed(){
    ?>
  <div class="notice notice-error is-dismissible">
  <p><?php esc_html_e( 'Invalid key. Please provide a valid key' , 'push-magnet-web-push-notifications'); ?></p>
  </div>
  <?php
}
function pushmagnet_authorization_failed(){
    ?>
  <div class="notice notice-error is-dismissible">
      <p><?php esc_html_e( 'You are not authorized for this operation' , 'push-magnet-web-push-notifications'); ?></p>
  </div>
  <?php
}