<?php $active_tab =CWSAdmin::mail_active_tab(); ?>
<div class="wrap">
    <h2><?php _e('Ultimate Post by Mail Configuration', 'cws'); ?></h2>
    <?php CWSAdmin::validateServer();?>
    <h2 class="nav-tab-wrapper">  
        <a href="?page=<?php echo CWS_MAIL_ADMINCONF; ?>&tab=server" class="nav-tab <?php echo $active_tab == 'server' ? 'nav-tab-active' : ''; ?>"><?php _e('Server Settings', 'cws') ?></a>  
        <a href="?page=<?php echo CWS_MAIL_ADMINCONF; ?>&tab=post" class="nav-tab <?php echo $active_tab == 'post' ? 'nav-tab-active' : ''; ?>"><?php _e('Post Settings', 'cws') ?></a>  
    </h2> 
    <form action=""  method="post">
        <input type="hidden" name="cws_update" value="1">
        <?php CWSAdmin::mail_configuration_page($active_tab);?>
        <?php submit_button(); ?>
    </form>
</div>
