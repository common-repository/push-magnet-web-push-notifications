<?php if( ! defined('ABSPATH') ) exit; ?>
<div class="push-magnet-container">
    <div class="push-magnet-card-grid" style="grid-template-columns: repeat(2, 1fr)">
        <div class="push-magnet-card">
            <div class="push-magnet-card-header"> <h4>Setup Push Magnet</h4> </div>
            <form action="" method="post">
                <div class="push-magnet-card-body">
                    <p>Please enter the API KEY in the field below. You can find this under Integration > WordPress in Push Magnet Web Console.</p>
                    <?php wp_nonce_field( 'pushmagnet_setup_nounce_action', 'pushmagnet_setup_nonce_field' ); ?>
                    <strong>API Key</strong>
                    <input type="text" class="push-magnet-textfield" name="push-magnet_api-key" id="push-magnet_api-key" placeholder="Enter API key" value="<?php echo esc_attr(get_option('pushmagnet_key')); ?>" />
                </div>
                <div class="push-magnet-card-footer">
                    <button type="submit" class="push-magnet-btn" name="frm_pushmagnet_setup">Activate Push Magnet</button>
                </div>
            </form>
        </div>
        <div class="push-magnet-card">
            <div class="push-magnet-card-header">
                <h4>New to Push Magnet</h4>
            </div>
            <div class="push-magnet-card-body">
                <button type="submit" class="push-magnet-btn" onclick="javascript:window.open('https:\/\/app.pushmagnet.com/create-account', '_target')">Register</button>
            </div>
        </div>
    </div>
</div>