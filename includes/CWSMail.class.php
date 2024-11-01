<?php

/**
 * Description of CWSMail
 * Created on 31-oct.-2015
 * @author Gerry Ntabuhashe & Comwes
 * @copyright Owner copyright 31-oct.-2015
 * @version 1.0
 */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class CWSMail {

    protected $imap, $login, $pass, $port, $security, $host;
    protected $protocol = 'imap';

    public function __construct($config) {
        foreach ($config as $key => $value) {
            if (!in_array($key, array('login', 'pass', 'port', 'security', 'host'))) {
                continue;
            }
            if ($key == 'security') {
                $this->$key = ($value) ? "/$value" : '';
                if ($value == 'ssl') {
                    $this->security .="/novalidate-cert";
                }
                continue;
            }
            $this->$key = $value;
        }
        $this->imap = imap_open(
                '{' . "$this->host:$this->port/$this->protocol$this->security" . '}/INBOX', $this->login, $this->pass, NULL, 1, array('DISABLE_AUTHENTICATOR' => 'PLAIN')
        );
        if (!$this->imap) {
            throw new Exception(__("Sorry, unable to connect to the mailbox. Please check if the credentials you have provided are valid.", 'cws'));
        }
    }

    public function numberOfMessages() {
        return imap_num_msg($this->imap);
    }

    public function isValid() {
        return !empty($this->imap);
    }

    public function close() {
        if ($this->imap) {
            imap_close($this->imap,CL_EXPUNGE);
        }
    }

    public function delete($msgid, $delete=true) {
        if ($this->imap && $delete) {
            imap_delete($this->imap, $msgid);
        }
    }
    public function getMail($msgid) {
        $body = $this->getStructure(imap_fetchstructure($this->imap, $msgid), $msgid);
        return array_merge(['header' => imap_header($this->imap, $msgid)], $body);
    }

    public function getStructure($struct, $msgid) {
        $parts = $this->create_part_array($struct);
        //Fetch all message for each part
        $body=[];
        foreach($parts as $section){
            if(in_array($section['part_object']->subtype,['ALTERNATIVE']) || !empty($body[$section['part_object']->subtype])){
                continue;
            }
            foreach($section['part_object']->parameters as $v){
                $body[$section['part_object']->subtype][$v->attribute]=$v->value;
            }
            $body[$section['part_object']->subtype]['content'] = $this->decode(imap_fetchbody ($this->imap , $msgid, $section['part_number']), $section['part_object']->encoding);
        }
        return $body;
    }

    function create_part_array($structure, $prefix = "") {
        if (!empty($structure->parts) && is_array($structure->parts)) {    // There some sub parts
            foreach ($structure->parts as $count => $part) {
                $this->add_part_to_array($part, $prefix . ($count + 1), $part_array);
            }
        } else {
            $part_array[] = array('part_number' => $prefix . '1', 'part_object' => $structure);
        }
        return $part_array;
    }

// Sub function for create_part_array(). Only called by create_part_array() and itself. 
    function add_part_to_array($obj, $partno, & $part_array) {
        $part_array[] = array('part_number' => $partno, 'part_object' => $obj);

        if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
//        if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
            if (array_key_exists('parts', $obj)) {
                foreach ($obj->parts as $count => $part) {
                    // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                    if (sizeof($part->parts) > 0) {
                        foreach ($part->parts as $count2 => $part2) {
                            $this->add_part_to_array($part2, $partno . "." . ($count2 + 1), $part_array);
                        }
                    } else {    // Attached email does not have a seperate mime attachment for text
                        $part_array[] = array('part_number' => $partno . '.' . ($count + 1), 'part_object' => $obj);
                    }
                }
            } else {    // Not sure if this is possible
//            $part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
                $part_array[] = array('part_number' => $partno . '.1', 'part_object' => $obj);
            }
        } else {    // If there are more sub-parts, expand them out.
//        if (sizeof($obj->parts) > 0) {
            if (array_key_exists('parts', $obj)) {
                foreach ($obj->parts as $count => $p) {
                    $this->add_part_to_array($p, $partno . "." . ($count + 1), $part_array);
                }
            }
        }
    }

    private function decode($text, $encoding_id) {
        if ($encoding_id == 4) {
            return quoted_printable_decode($text);
        } elseif ($encoding_id == 3) {
            return imap_base64($text);
        } 
        return $text;
    }

}
