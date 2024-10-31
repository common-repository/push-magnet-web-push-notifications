<?php if( ! defined('ABSPATH') ) exit; ?>
<div class="push-magnet-container">
    <div class="push-magnet-card">
        <div class="push-magnet-card-body">
            <h3 class="">Push Magnet Configuration</h3>
        </div>
    </div>
    <div class="push-magnet-card">
        <div class="push-magnet-card-header">
            <h4>Post Settings</h4>    
        </div>
        <form method="POST" action="">
        <?php wp_nonce_field( 'pushmagnet_configuration_nounce_action', 'pushmagnet_configuration_nonce_field' ); ?>
            <div class="push-magnet-card-body">
                <table>
                    <tbody>
                        <tr>
                            <th><label for=""></label></th>
                            <td><input type="checkbox" name="pushmagnet_enable" id="pushmagnet_enable" <?php if(get_option('pushmagnet_enable') == 'on') { ?> checked="checked" <?php } ?> /><strong>Enable Web Push</strong></td>
                        </tr>
                        <tr>
                            <th><label for="pushmagnet_post_title">Notification Title</label></th>
                            <td><input type="text" name="pushmagnet_post_title" id="pushmagnet_post_title" value="<?php echo esc_attr(get_option('pushmagnet_post_title')); ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="pushmagnet_post_message">Notification Message</label></th>
                            <td><input type="text" name="pushmagnet_post_message" id="pushmagnet_post_message" value="<?php echo esc_attr(get_option('pushmagnet_post_message')); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="">Post Type</label></th>
                            <td>
                            <?php 
                                                $post_args = array(
                                                    'public'   => true,
                                                    '_builtin' => false,
                                                );
                                                $getCustomPostsList = get_post_types($post_args);
                                                
                                                //add POST and PAGE builtin post types
                                                $getCustomPostsList['post'] = 'post';
                                                $getCustomPostsList['page'] = 'page';

                                                $selectedPostTypes = @json_decode(get_option('wpp_post_type'));
                                                if( ! $selectedPostTypes )
                                                    $selectedPostTypes = array('post','page');
                                            ?>                                
                                            <select name="pushmagnet_post_type[]" class="chosen-select" required>
                                                <?php foreach( $getCustomPostsList as $pt){?>
                                                    <option <?php if(  (!$selectedPostTypes && $pt == 'post') || (in_array($pt,$selectedPostTypes) ) ){ ?> selected <?php } ?>  value="<?php echo esc_attr($pt);?>"><?php echo esc_html($pt);?></option>
                                                <?php } ?>
                                            </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="pushmagnet_post_icon">Notification Icon</label></th>
                            <td><input type="text" name="pushmagnet_post_icon" id="pushmagnet_post_icon" value="<?php echo esc_attr(get_option('pushmagnet_post_icon')); ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="pushmagnet_post_image">Notification Image</label></th>
                            <td><input type="text" name="pushmagnet_post_image" id="pushmagnet_post_image" value="<?php echo esc_attr(get_option('pushmagnet_post_image')); ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="pushmagnet_utm_parameter">UTM Parameters</label></th>
                            <td><input type="text" name="pushmagnet_utm_parameter" id="pushmagnet_utm_parameter" value="<?php echo esc_attr(get_option('pushmagnet_utm_parameter')); ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="pushmagnet_post_segments">Select Segment</label></th>
                            <td><input type="text" name="pushmagnet_post_segments" id="pushmagnet_post_segments" value="<?php echo esc_attr(get_option('pushmagnet_post_segments')); ?>" /></td>
                        </tr>
                        <!-- <tr>
                            <th><label for="">Automatic Notifications</label></th>
                            <td><input type="text" name="" id=""></td>
                        </tr>
                        <tr>
                            <th><label for="">Autohide Notification</label></th>
                            <td><input type="text" name="" id=""></td>
                        </tr>
                        <tr>
                            <th><label for="">Manual Integration</label></th>
                            <td><input type="text" name="" id=""></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="push-magnet-card-footer">
                <button type="submit" class="push-magnet-btn" name="pushmagnet_save_settings">Save Settings</button>
            </div>
        </form>
    </div>
</div>