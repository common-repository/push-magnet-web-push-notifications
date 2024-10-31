<?php if( ! defined('ABSPATH') ) exit; ?>
<div class="push-magnet-container">
    <div class="push-magnet-card">
        <div class="push-magnet-card-body">
            <h3 class="">Push Magnet Dashboard</h3>
        </div>
    </div>

    <div class="push-magnet-card">
        <div class="push-magnet-card-body">
            <h3>New Users</h3>
            <div class="push-magnet-chart-container">
                <canvas id="line-chart" role="img" aria-label="line chart of new users."></canvas>
            </div>
        </div>
    </div>

    <div class="push-magnet-card-grid">
        <div class="push-magnet-card">
            <div class="push-magnet-card-body">
                <h6>Active Users</h6>
                <div class="push-magnet_active_subscribers"></div>
            </div>
        </div>
        <div class="push-magnet-card">
            <div class="push-magnet-card-body">
                <h6>Optin Rate</h6>
                <div class="push-magnet_optin"></div>
                <a href="https://app.pushmagnet.com/" target="_blank">See all <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg></a>
            </div>
        </div>
        <div class="push-magnet-card">
            <div class="push-magnet-card-body">
                <h6>Total Lifetime Users</h6>
                <div class="push-magnet_total_subscribers"></div>
                <a href="https://app.pushmagnet.com/" target="_blank">See all <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg></a>
            </div>
        </div>
    </div>
    <div class="push-magnet-card-grid">
        <div class="push-magnet-card">
            <div class="push-magnet-card-header">
                <h6>Browers</h6>
            </div>
            <div class="push-magnet-card-body push-magnet-browsers push-magnet-padding-0">
                
            </div>
        </div>
        <div class="push-magnet-card">
            <div class="push-magnet-card-header">
                <h6>Devices</h6>
            </div>
            <div class="push-magnet-card-body push-magnet-device push-magnet-padding-0">
                
            </div>
        </div>
        <div class="push-magnet-card">
            <div class="push-magnet-card-header">
                <h6>Operating Systems</h6>
            </div>
            <div class="push-magnet-card-body push-magnet-os push-magnet-padding-0">
                
            </div>
        </div>
    </div>
    <div class="push-magnet-card">
            <div class="push-magnet-card-header">
                <h5>Recent Subscription Activity</h5>
            </div>
            <div class="push-magnet-card-body push-magnet-subscribers push-magnet-padding-0">
                
            </div>
        </div>
</div>

<script defer>
    jQuery(document).ready(function(){
        <?php 
            $end_point = 'https://app.pushmagnet.com/v1/dashboard';
            $dashboard = pushmagnet_api_request($end_point);
        ?>
        data = JSON.parse(<?php echo wp_json_encode($dashboard['response_json']) ?>);
        const lineChart = new window.Chart(document.getElementById('line-chart'), {
            type: "line",
            data: {
                labels: data.subscribers.day,
                datasets: [{
                    data: data.subscribers.count,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    borderColor: "rgb(255, 0, 0)",
                    backgroundColor: "rgb(255, 0, 0, 0.1)"
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        display: false,
                        min: 0,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'x'
                }
            }
        });

        jQuery(".push-magnet_active_subscribers").text(data.subscribers.active);
        jQuery(".push-magnet_total_subscribers").text(data.subscribers.total);
        jQuery(".push-magnet_optin").text(data.optin);

        jQuery(".push-magnet-browsers").html(data.browsers);
        jQuery(".push-magnet-device").html(data.device);
        jQuery(".push-magnet-os").html(data.os);
        jQuery(".push-magnet-subscribers").html(data.subscribers.recent);
    })
</script>