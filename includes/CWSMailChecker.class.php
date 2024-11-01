<?php

/**
 * Description of CWSMailChecker
 * Created on 30-oct.-2015
 * @author Gerry Ntabuhashe & Comwes
 * @copyright Owner copyright 30-oct.-2015
 * @version 1.0
 */
class CWSMailChecker {

    private static $initiated = false;

    public static function init() {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }
    /**
     * This function registers actions and initialize hooks.
     */
    public static function init_hooks() {
        self::$initiated = true;
        add_action('wp-mail.php', array('CWSMailChecker', 'check_mails'));
        add_action('wp_footer', array(__CLASS__, 'addFooter'));  
        add_action('wp_footer', array(__CLASS__, 'addFooter'));  
        add_action( 'wp_enqueue_scripts', array(__CLASS__,'addJs') );
        add_action( 'admin_enqueue_scripts', array(__CLASS__,'addJs') );
    }
    /**
     * This function 
     */
    public static function check_mails() {
        if (!defined('WP_MAIL_INTERVAL'))
            define('WP_MAIL_INTERVAL', 1 * 600); // 10 minutes interval, make it Configurable?
        $last_checked = get_transient('mailserver_last_checked');
        if ($last_checked) {
            echo (__('Hey slow down, try again later!', 'cws'));
            exit;
        }
        set_transient('mailserver_last_checked', true, WP_MAIL_INTERVAL);
        $config = self::get_server_config();
        try {
            $mbox = new CWSMail($config);
            $count = min(array(10, $mbox->numberOfMessages()));
        } catch (Exception $e) {
            echo $e->getMessage();
            $mbox->close();
            exit;
        }
        $gmt_offset = get_option('gmt_offset');
        define('CWS_MAIL_AUTHOR', self::mail_options('cws_mail_default_author'));
        for ($i = 1; $i <= $count; ++$i) {
            $mail = $mbox->getMail($i);
            $timestamp = strtotime($mail['header']->date);
            $post_date_gmt = gmdate('Y-m-d H:i:s', $timestamp) . "\n";
            $post_date = date('Y-m-d H:i:s', $timestamp + ($gmt_offset * 3600)) . "\n";
            if (self::is_spam($mail['header'])) {
                $mbox->delete($i);
                echo "Mail seems to be a spam\n";
                continue;
            }
            //Get Author information
            $author = self::get_author($mail['header']);
            foreach ($author as $k => $v) {
                $$k = $v;
            }
            //Get Title and Category information
            $post_data = self::get_post_data($mail['header']->subject);
            foreach ($post_data as $k => $v) {
                $$k = $v;
            }
            //Get post content.
            $post_content = self::get_post_content($mail['PLAIN'], $mail['HTML']);
            //Post data
            $post_data = compact('post_content', 'post_title', 'post_date', 'post_date_gmt', 'post_author', 'post_category', 'post_status');
            //$post_data = add_magic_quotes($post_data);
            $post_ID = wp_insert_post($post_data, true);
            if (is_wp_error($post_ID)) {
                echo $post_ID->get_error_message() . "\n";
                continue;
            }
            /* We couldn't post, for whatever reason. Better move forward to the next email. */
            if (empty($post_ID)) {
                echo 'Sorry, $post_ID is empty.'."\n";
                continue;
            }
            do_action('publish_phone', $post_ID);
            echo sprintf(__('Author: %s', 'cws'), esc_html($post_author)) . "\n" ;
            echo sprintf(__('Post title: %s', 'cws'), esc_html($post_title)) . "\n\n" ;
            $mbox->delete($i);
        }
        $mbox->close();
        exit;
    }

    /**
     * This function formates mail content to be published on the blog.
     * @param string $plain text content if present in the mail
     * @param string $html content contained in the mail
     * @return string The content of mail formated to be published on the blog.
     */
    private static function get_post_content($plain, $html) {
        $is_plain = false;
        $content = $html;
        if (empty($content) || empty($content['content'])) {
            $is_plain = true;
            $content = $plain;
        }
        if (empty($content) || empty($content['content'])) {
            return __("This post does not contain contents.");
        }
        $post_content = $content['content'];
        if (function_exists('iconv') && !empty($content['charset'])) {
            $post_content = iconv($content['charset'], get_option('blog_charset'), $post_content);
        } else {
            $post_content = utf8_encode($post_content);
        }
        $post_content = self::strip_styles($post_content, $is_plain);
        if (self::mail_options('cws_mail_convertlinks')) {
            $post_content = self::replace_url($post_content);
        }
        return $post_content;
    }

    /**
     * This function replaces urls by a given links version.
     * @param string $str o convert.
     * @return string The string with url replaced by links.
     */
    private static function replace_url($str) {
        $match = $replace = [];
        $urls = [];
        //$regex = "/(http:\/\/[^ )<\r\n]+)|(https:\/\/[^ )<\r\n]+)/i";
        $regex = '/https?\:\/\/[^\" <\n\r]+/i';
        preg_match_all($regex, $str, $urls);
        if (is_array($urls[0])) {
            foreach ($urls[0] as $url) {
                $match[] = $url;
                $replace[] = "<a href=\"$url\" target=\"_blank\">$url</a>";
            }
        } else {
            return $str;
        }
        return str_replace($match, $replace, $str);
    }

    /**
     * This function removes all style and tags from a string, only keeping br and p tags.
     * @param string $str where the style has to be removed.
     * @return string The string with sanitized with style and tags removed
     */
    private static function strip_styles($str, $is_plain=false) {
        if($is_plain){
            $str = str_replace(["\n ", '&nbsp; '], ["\n"], $str);
            $str = preg_replace('/(\r\n|\n|\r)/', "\n", $str);
            $str = preg_replace('@ +@', ' ', $str); // replace where there's more than one space
            $str = preg_replace("@\n+@", "\n", $str);   
            return nl2br($str);
        }
        //$str = str_replace(['<div', '</div>'],['<p', '</p>'], $str);
        $str = str_replace(['&nbsp;','<P>',"</P>\n","</P>"],[' ','<p>','</p>','</p>'], $str);
        $str = preg_replace('/(<style>.+?)+(<\/style>)/i', "", $str);
        $str = preg_replace('#(<[a-zA-Z0-9 ]*)(id=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $str);
        $str = preg_replace('#(<[a-zA-Z0-9/=\."\-;: ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $str);
        $str = strip_tags($str, '<p><br>');
        $str = str_replace([' >', '<br>', '<p><p>', '</p></p>', '&nbsp;',"\t"], ['>', " \n<br /> ", '<p>', '</p>', ' ',' '], $str);
        $str = preg_replace('/(\r\n|\n|\r)/', "\n", $str);
        $str = preg_replace('@ +@', ' ', $str); // replace where there's more than one space
        $str = preg_replace("@\n+@", "\n", $str);
        $str = str_replace(["\n ", '<br />'], ["\n"], $str);
        $str = preg_replace("@\n+@", "\n", $str);
        $str = str_replace(["\n ","</p> \n"], ["\n",'</p>'], $str);
        return $str;
    }

    /**
     * This function fetcht information about the title and the category of post.
     * @param string $subject The subject of the meceived mail.
     * @return array Formated with key post_title containing the title, post_category an array of categories id.
     */
    private static function get_post_data($subject) {
        if (function_exists('iconv_mime_decode')) {
            $subject = wp_strip_all_tags(trim(iconv_mime_decode($subject, 2, get_option('blog_charset'))));
        } else {
            $subject = wp_strip_all_tags(trim(wp_iso_descrambler($subject)));
        }
        $subarray = explode(']]', $subject); // The separator to use.
        //This is shared with the wordpress writting section
        $default_category = get_option('default_email_category');
        if (count($subarray) <= 1) {
            $post_title = $subject;
            $category = $default_category;
        } else {
            list($category_name, $post_title) = $subarray;
            $category_name = trim($category_name);
            if (is_term($category_name, 'category')) {
                $category = get_cat_ID($category_name);
            } else {
                //Keep it to the default category value.
                $category = $default_category;
            }
        }
        return [
            'post_title' => trim($post_title),
            'post_category' => explode(',', $category),
        ];
    }

    /**
     * This function returns information related to the author of the mail.
     * @param object $header is the header return by the imap object.
     * @return array An array containing post_author, need_moderation and post_status values.
     */
    private static function get_author($header) {
        $need_moderation = false;
        $mail = sanitize_email(trim($header->from[0]->mailbox . "@" . $header->from[0]->host));
        if (is_email($mail)) {
            $userdata = get_user_by_email($mail);
            if (empty($userdata)) {
                $author_found = false;
            } else {
                $post_author = $userdata->ID;
                $author_found = true;
            }
        } else {
            //we need moderation to prevent spams
            $author_found = false;
        }
        if (!$author_found) {
            $need_moderation = true;
            $post_author = CWS_MAIL_AUTHOR;
        }
        $post_status = self::mail_options('cws_mail_status');
        if ($author_found) {
            $user = new WP_User($post_author);
            //publish post if user have capability to do so.
            $post_status = ( $user->has_cap('publish_posts') ) ? 'publish' : $post_status;
        }

        return[
            'post_author' => $post_author,
            'need_moderation' => $need_moderation,
            'post_status' => $post_status
        ];
    }

    /**
     * This function checks if the mail looks like a SPAM by analyzing headers
     * @param object $header is the header return by the imap object.
     * @return boolean Returns true if looks like a spam and false otherwise.
     */
    private static function is_spam($header) {
        $to = trim($header->to[0]->mailbox . "@" . $header->to[0]->host);
        $from = trim($header->from[0]->mailbox . "@" . $header->from[0]->host);
        $sender = trim($header->sender[0]->mailbox . "@" . $header->sender[0]->host);
        if (empty($header->from[0]->mailbox) || empty($header->from[0]->host) || $to == '@' || $from == '@' || $sender == '@') {
            return true;
        }
        if ($to == $from || $from != $sender) {
            return true;
        }
        if (empty($header->subject) || substr_count($header->subject, 'TR:') || substr_count($header->subject, 'FWD:')) {
            return true;
        }
        return false;
    }
    /**
     * This function returns the mail server configuration
     * @return array An array which contains mail server configuration
     */
    public static function get_server_config() {
        $config = [];
        foreach (array('cws_mail_pass', 'cws_mail_server_type',
    'cws_mail_host', 'cws_mail_security', 'cws_mail_port', 'cws_mail_login') as $key) {
            $config[str_replace('cws_mail_', '', $key)] = get_option($key);
        }
        return $config;
    }
    /**
     * This function is used to fetch parameter options
     * @param string $item the option to fetch.
     * @return string The value of the option $item
     */
    public static function mail_options($item) {
        $array = $_POST;
        $defaults = [];
        parse_str(CWS_MAIL_DEFAULT, $defaults);
        if (!empty($array[$item])) {
            return $array[$item];
        }
        
        if (!get_option($item)) {
            if ($item == 'cws_mail_pass') {
                $a = $defaults[$item];
            }
            $a = str_replace(' ', '+', $defaults[$item]);
            if (!empty($a)) {
                return $a;
            }
        }
        return get_option($item);
    }
    public static function addJs(){
        if(self::mail_options('cws_mail_add_iframe') == 2) {
            wp_enqueue_script( 'cws_mail_public_side', plugins_url('/assets/js/cws_loader.js',__DIR__) );    
        }
    }
    public static function addFooter(){
        $fetch_type = self::mail_options('cws_mail_add_iframe');
        if($fetch_type==1){
            echo '<script type="text/javascript">';
            echo "document.write('".sprintf('<iframe src="%1$s" style="margin:0px; padding:0px;width:0px;height:0px;" name="mailiframe" width="0" height="0" frameborder="0" scrolling="no" title=""></iframe>',  get_bloginfo('url').'/wp-mail.php')."');";
            echo '</script>';
	}
    }
    

}
