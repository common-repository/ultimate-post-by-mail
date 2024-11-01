<?php

/**
 * Description of CWSAdmin
 * Created on 30-oct.-2015
 * @author Gerry Ntabuhashe & Comwes
 * @copyright Owner copyright 30-oct.-2015
 * @version 1.0
 */
class CWSAdmin {

    private static $initiated = false;
    /**
     * Function that initialize all this.
     */
    public static function init() {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }
    /**
     * This function will be used to initialize various hooks and action for the admin side.
     */
    public static function init_hooks() {
        self::$initiated = true;
        define('CWS_MAIL_ADMINCONF','cws-mail-configuration');
        add_action( 'admin_enqueue_scripts', array('CWSAdmin','addJs') );
        add_action('admin_menu', array('CWSAdmin', 'mail_configuration'));
    }
    /**
     * 
     * @param string $check the filter to check agains.
     * @param string $value the value to test
     * @return boolean TRUE if the value is valid. Or false other wise. 
     * Will return true if the check is not (empty or numeric).
     */
    public static function isValid($check, $value){
        if($check == 'empty'){
            return !empty($value);
        } elseif($check == 'numeric'){
            return is_numeric($value);
        }
        return true;
    }
    /**
     * This function adds the Configuration entry in the settings tab.
     */
    public static function mail_configuration() {
        add_options_page('U Post by Mail', 'U Post by Mail', 'manage_options', 'cws-mail-configuration', array('CWSAdmin', 'mail_post_configuration'));
    }
    /**
     * This function provide which configuration tab is currently active.
     * @return string the active configuration tab.
     */
    public static function mail_active_tab(){
        $tab = $_GET['tab'];
        if(empty($tab) || !in_array($tab, array('server','post'))){
            return 'server';
        }
        return $tab;
    }
    /**
     * This function displays the mail configuration page view.
     * @param string $tab the active tab. Can be set to either server(default) or post
     */
    public static function mail_configuration_page($tab) {
        if(in_array($tab, array('server','post'))){
            include_once CWS_MAIL_DIR.'admin/CWSMailConfiguration'.ucfirst($tab).'.view.php';
            return;
        } 
        include_once CWS_MAIL_DIR.'admin/CWSMailConfiguration'.ucfirst('server').'.view.php';
    }
    /**
     *  This function includes the post configuration views.
     */
    public static function mail_post_configuration() {
        #Configuration menu will be displayed here.
        include_once CWS_MAIL_DIR . '/admin/CWSMailConfiguration.view.php';
    }
    /**
     * This function saves the configuration related to the mail server.
     * @param array $array contains all configuration to be saved or updated.
     * 
     */
    public static function mail_server_config_update($array) {
        if (!isset($array['cws_update'])) {
            return;
        }
        $errors = [
            'host' => ['check'=>'empty','message'=>__('Provide a valid imap hostname.','cws')],
            'port' => ['check'=>'numeric','message'=>__('Port number is empty.','cws')],
            'login' => ['check'=>'empty','message'=>__('The login is mandatory to connect to your mailbox.','cws')],
            'pass' => ['check'=>'empty','message'=>__('The password is mandatory to connect to your mailbox.','cws')],
        ];
        unset ($array['cws_update']);
        unset ($array['submit']);
        parse_str(CWS_MAIL_DEFAULT, $defaults);
        $config = array();
        $error = [];
        foreach ($array as $key => $value) {
            $rkey = str_replace('cws_mail_','',$key);
            if(!empty($errors[$rkey]['check']) && !self::isValid($errors[$rkey]['check'],$value)){
                $error[] = $errors[$rkey]['message'];
            }
            $config[$rkey] = $value;
        }
        if(!empty($error)){
            self::displayNotice(implode('<br>', $error),'error');
            return;
        }
        try {
            $mbox = new CWSMail($config);
            $mbox->close();
        } catch(Exception $e){
            self::displayNotice($e->getMessage(),'error');
        }
        //print_r($array);
        foreach($array as $key=>$value){
            if (!get_option($key)) {
                add_option($key, $value, '', 'no');
                echo "$key=>$value";
            } else {
                update_option($key, $value);
            }
        }
        unset($_POST);
        if (!get_option('cws_mail_server_configured')){
            add_option('cws_mail_server_config','1', '', 'no');
        } else {
            update_option('cws_mail_server_config', '1');
        }
        self::displayNotice(__('Your configuration has been successfully saved!','cws'));
    }
    /**
     * 
     * This function saves varius action to perform on received mails.
     * @param array $array containing options to be updated or created.
     * 
     */
    public static function mail_post_config_update($array) {
        if (!isset($array['cws_update'])) {
            return;
        }
        unset ($array['cws_update']);
        unset ($array['submit']);
        foreach ($array as $key=>$value){
            if (get_option($key) !== false) {
                update_option($key, $value);
            } else {
                add_option($key, $value, '', 'no');
            }
        }
        if (!get_option('cws_mail_post_config')){
            add_option('cws_mail_post_config','1', '', 'no');
        } else {
            update_option('cws_mail_post_config', '1');
        }
        unset($_POST);
        self::displayNotice(__('Your configuration has been successfully saved!','cws'));
    }
    /**
     * This function print option to select which host will be used to fetch messages.
     * @return string The HTML code representing these options.
     */
    public static function mail_type() {
        $types = array(
            //'gmail' => array('name' => 'Gmail', 'cws_mail_host' => 'imap.gmail.com', "cws_mail_port" => "993", "cws_mail_security" => "ssl"),
            'yahoo' => array('name' => 'Yahoo!', 'cws_mail_host' => 'imap.mail.yahoo.com', "cws_mail_port" => "993", "cws_mail_security" => "ssl"),
            'live'=>array('name'=>'Outlook','cws_mail_host'=>"imap-mail.outlook.com", "cws_mail_port"=>"993", "cws_mail_security"=>"ssl"),
            'aol' => array('name' => 'AOL', 'cws_mail_host' => "imap.aol.com", "cws_mail_port" => "143", "cws_mail_security" => "notls"),
            'other' => array('name' => 'Other', 'cws_mail_host' => "mail.yourhost.com", "cws_mail_port" => "143", "cws_mail_security" => "notls")
        );
        $echo = "";
        foreach ($types as $type => $settings) {
            $sel = "";
            foreach ($settings as $key => $value) {
                $$key = $value;
            }
            if ($type == CWSMailChecker::mail_options("cws_mail_server_type")) {
                $sel = "checked='checked'";
            }
            $echo .= "<input name='cws_mail_server_type' class='cws_mail_server_type' type = 'radio' value='$type' $sel  onclick=\"cwsMailChange('$type','$cws_mail_host','$cws_mail_security','$cws_mail_port'); return;\" /> $name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        return $echo;
    }
    public static  function validateServer(){
        if(!function_exists('imap_open')){
            echo '<div class="error"><p>'.__('Sorry, you must have the PHP5-IMAP extension installed and enabled to use this Plugin.','cws')
                    .'<br><em>'.__("To fix this, please install the module or request its activation by your server administrator.",'cws').'</em>'
                    .'</p></div>';
        }
    }

    /**
     * This function displays a given message.
     * @param string $message the message to display print
     * @param string $class the class of the container. Set to updated by default.
     */
    public static function displayNotice($message, $class='updated'){
        echo "<div class=\"$class\"><p><strong>".$message ."</strong></p></div>";        
    }
    /**
     * This function adds Javascript needed for the admin side.
     */
    public static function addJs(){
        wp_enqueue_script( 'cws_mail_admin_side', plugins_url('/assets/js/admin-side.js',__DIR__) );
    }
}
