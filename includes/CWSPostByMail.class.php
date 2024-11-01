<?php

/**
 * Description of CWSAdmin
 * Created on 30-oct.-2015
 * @author Gerry Ntabuhashe & Comwes
 * @copyright Owner copyright 30-oct.-2015
 * @version 1.0
 */
class CWSPostByMail {

    private static $initiated = false;
    
    /**
     * This function executes actions when the plugin is activated.
     */
    public static function plugin_activation() {
        self::send_stats(['plugin'=>__CLASS__,'install']);         
    }
    
    /**
     * This function executes various action on the desactivation of the plugin.
     */
    public static function plugin_deactivation() {
        $array=[];
        self::send_stats(['plugin'=>__CLASS__,'activate']);
        parse_str(CWS_MAIL_DEFAULT,$array);
        foreach ($array as $key => $value) {
            if(substr_count($key, 'cws_mail_')) {
                delete_option($key);
            }
        }
    }
    
    public static function send_stats($args){
        wp_remote_post( 'https://api.comwes.eu/wp_plugins', $args);
    }
    
}
