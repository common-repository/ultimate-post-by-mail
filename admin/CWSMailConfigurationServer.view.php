<?php

/**
 * @name CWSMailConfigurationStatus.view.php
 * Created on 30-oct.-2015
 * @author Gerry Ntabuhashe & Comwes
 * @copyright Owner copyright 30-oct.-2015
 * @version 1.0
 */
CWSAdmin::mail_server_config_update($_POST);
?>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_server_type"><?php _e('Your mail provider', 'cws') ?></label></th>
            <td><?php echo CWSAdmin::mail_type(); ?>
                <br>
                <small><em><?php _e('Select your mail host', 'cws'); ?></em></small>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_host"><?php _e('Your Imap Server', 'cws') ?></label></th>
            <td><input type="text" class="regular-text"value="<?php echo CWSMailChecker::mail_options('cws_mail_host') ?>" name="cws_mail_host" id="cws_mail_host"/><br>
                <small><em><?php _e('Set to <b>localhost</b> by default. It can be mail.yourdomain.com, imap.youdomain.com', 'cws'); ?></em></small>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_security"><?php _e('Security', 'cws') ?></label></th>
            <td><input type="text" class="regular-text"value="<?php echo CWSMailChecker::mail_options('cws_mail_security') ?>" name="cws_mail_security" id="cws_mail_security"/><br>
                <small><em><?php _e('Sets to <b>notls</b> by default, can be also <b>ssl, tls or notls</b>', 'cws'); ?></em></small>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_port"><?php _e('Server port', 'cws') ?></label></th>
            <td><input type="text" class="regular-text"value="<?php echo CWSMailChecker::mail_options('cws_mail_port') ?>" name="cws_mail_port" id="cws_mail_port"/><br>
                <small><em><?php _e('Enter your imap server port. <b>143</b> by default', 'cws'); ?></em></small></td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_login"><?php _e('Your login', 'cws') ?></label></th>
            <td><input type="text" class="regular-text"value="<?php echo CWSMailChecker::mail_options('cws_mail_login') ?>" name="cws_mail_login" id="cws_mail_login"/><br>
                <small><em><?php _e('Enter here the login you use to access your e-mail account box', 'cws'); ?></em></small></td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_pass"><?php _e('Your password', 'cws') ?></label></th>
            <td><input type="password" class="regular-text" value="<?php echo CWSMailChecker::mail_options('cws_mail_pass') ?>" name="cws_mail_pass" id="cws_mail_pass"/><br>
                <small><em><?php _e('Enter here the password related to your login e-mail account', 'cws'); ?></em>
                </small></td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_add_iframe"><?php _e('Use iFrame to check post', 'cws');?></label></th>
            <td><input type="radio" class="" value="1" name="cws_mail_add_iframe" <?php if (CWSMailChecker::mail_options("cws_mail_add_iframe") == 1) echo "checked='yes'"; ?> /> Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input 
                    type="radio" class="" value="0" name="cws_mail_add_iframe" <?php if (CWSMailChecker::mail_options('cws_mail_add_iframe') == 0) echo "checked='yes'"; ?> /> No<br>
                <small><em><?php _e('In order to check for mails, each time a page of your blog is loaded.', 'cws'); ?></em>
                </small></td>
        </tr>
    </tbody>
</table>

