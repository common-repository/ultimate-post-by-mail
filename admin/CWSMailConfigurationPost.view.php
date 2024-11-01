<?php
/**
 * @name CWSMailConfigurationServer.view.php
 * Created on 30-oct.-2015
 * @author Gerry Ntabuhashe & Comwes
 * @copyright Owner copyright 30-oct.-2015
 * @version 1.0
 */
CWSAdmin::mail_post_config_update($_POST);
?>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_status"><?php _e('Publish mail as', 'cws') ?></label></th>
            <td>
                <?php wp_dropdown_users([
                    'show_option_none'=>__('Select author', 'cws'),
                    'name' => 'cws_mail_default_author',
                    'selected' => CWSMailChecker::mail_options('cws_mail_default_author')
                    ]); ?><br>
                <small><em><?php _e('Select the author to use when the mail used is not registered.', 'cws'); ?></em></small></td>
            </td>
            
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_status"><?php _e('Default Mail Category') ?></label></th>
            <td>
                <?php wp_dropdown_categories([
                    'name' => 'default_email_category',
                    'hide_empty' => 0,
                    'orderby'=>'NAME',
                    'selected' => CWSMailChecker::mail_options('default_email_category')
                    ]); ?><br>
                <small><em><?php _e('Select the default category to use for mails.', 'cws'); ?></em></small></td>
            </td>
            
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_status"><?php _e('Status of posted mail', 'cws') ?></label></th>
            <td>
                <input type="radio" name="cws_mail_status" value="pending" <?php  if (CWSMailChecker::mail_options('cws_mail_status')=='pending') { echo 'checked="checked"'; }   ?>/> <?php _e('Pending', 'cws') ?><br />
                <input type="radio" name="cws_mail_status" value="publish" <?php  if (CWSMailChecker::mail_options('cws_mail_status')=='publish') { echo 'checked="checked"'; }   ?>/> <?php _e('Published', 'cws') ?><br />
                <small><em><?php _e('Select the status to give to post sent to the mail adress by unknown or contributors users.', 'cws'); ?></em></small></td>
        </tr>
        <tr valign="top">
            <th scope="row"> <label for="cws_mail_status"><?php _e('Convert Links', 'cws') ?></label></th>
            <td>
                <input type="radio" name="cws_mail_convertlinks" value="1" <?php  if (CWSMailChecker::mail_options('cws_mail_convertlinks')) { echo 'checked="checked"'; }   ?>/> <?php _e('Allways convert url', 'cws') ?><br />
                <input type="radio" name="cws_mail_convertlinks" value="0" <?php  if (!CWSMailChecker::mail_options('cws_mail_convertlinks')) { echo 'checked="checked"'; }   ?>/> <?php _e('Never convert url', 'cws') ?><br />
                <small><em><?php _e('Select if you want to convert url into links or not. Set to never by default.', 'cws'); ?></em></small></td>
        </tr>

    </tbody>
</table>
