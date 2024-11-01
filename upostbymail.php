<?php

/*
  Plugin Name: Ultimate Post by Mail
  Description: Ultimate Post by Mail allows blog owner to offset the deficiency of the wordpress post by mail feature. It would be useful for those who want to post on their blog by mail or those who want to allow people to publish post anonymously by just sending emails.
  Version:     2.0.0
  Author:      Comwes
  Author URI:  http://www.comwes.eu
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain: cws


  {Plugin Name} is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  {Plugin Name} is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with {Plugin Name}. If not, see {License URI}.
 */

define('WP_DEBUG',true);
if (!function_exists('add_action')) {
    echo "Kwaheri rafiki yangu!";
    exit;
}
define ('CWS_MAIL_DIR',plugin_dir_path(__FILE__));
define('CWS_MAIL_DEFAULT','cws_mail_convertlinks=0&cws_mail_status=pending&cws_mail_h=&cws_mail_w=&cws_mail_moderate=1&cws_mail_status=pending&cws_mail_default_author=1&cws_mail_login='
        .get_option('mailserver_login').'&cws_mail_host=localhost&cws_mail_pass='.get_option('mailserver_pass').
        '&cws_mail_port=143&cws_mail_server_type=other&cws_mail_protocol=notls&cws_mail_add_iframe=0');
load_plugin_textdomain ( 'cws', false, CWS_MAIL_DIR.'languages');

register_activation_hook( __FILE__, array( 'CWSPostByMail', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'CWSPostByMail', 'plugin_deactivation' ) );

foreach (glob(CWS_MAIL_DIR . "includes/*.class.php") as $file) {
    require_once $file;
    add_action( 'init', array( 'CWSMailChecker', 'init' ));
}

if (is_admin()) {
    foreach (glob(CWS_MAIL_DIR . "admin/*.class.php") as $file) {
        require_once $file;
    }
    add_action( 'init', array( 'CWSAdmin', 'init' ));
} 