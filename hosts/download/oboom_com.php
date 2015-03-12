<?php
if (!defined('RAPIDLEECH')) {
        require_once('index.html');
        exit();
}
class oboom_com extends DownloadClass {
        private $page, $cookie;
        public function Download($link) {
                global $premium_acc;
                if (strpos($link, '#')) $link = str_replace('#', '', $link);
                if(strpos($link, "/folder/")) {
                        if (!preg_match('@https?://(www.)?oboom\.com\/folder\/([\w]{8})@i', $link, $id)) html_error('Link invalid?.');
                        $link = "https://www.oboom.com/folder/$id[2]";
                        return $this->Folder($link);
                }
                if (!preg_match('@https?://(www.)?oboom\.com\/([\w]{8})@i', $link, $id)) html_error('Link invalid?.');
                $link = "https://www.oboom.com/$id[2]";
                if (($_REQUEST["premium_acc"] == "on" && $_REQUEST["premium_user"] && $_REQUEST["premium_pass"]) || ($_REQUEST["premium_acc"] == "on" && $premium_acc["oboom_com"]["user"] && $premium_acc["oboom_com"]["pass"])) {
                        $this->Login($link);
        } else {
                        $this->FreeDL($link);
                }
        }
        private function Login($link) {
                global $premium_acc;
                $pA = (!empty($_REQUEST['premium_user']) && !empty($_REQUEST['premium_pass']) ? true : false);
                $user = ($pA ? $_REQUEST['premium_user'] : $premium_acc['oboom_com']['user']);
                $pass = ($pA ? $_REQUEST['premium_pass'] : $premium_acc['oboom_com']['pass']);
                if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty. Please check login data.');
                $mysalt = strrev($pass);
                $hash = $this->pbkdf2('sha1', $pass, $mysalt, 1000, 16);
                $post = array();
                $post = array(
                        'auth' => $user,
                        'pass' => $hash,
                        'source' => '/#app',
                );
                $page = $this->GetPage('https://www.oboom.com/1/login', array('lang' => 'EN',), $post);
                list ($header, $page) = array_map('trim', explode("\r\n\r\n", $page, 2));
                $json = @json_decode($page, true);
                if ($json[0] == 200) {
                        $this->cookie = array('user' => urlencode($json[1]['cookie']), 'lang' => 'EN',);
                        $this->changeMesg(lang(300).'<br /><b>Account is premium</b><br />'.$json[1]['user']['premium']);
                        return $this->PremiumDL($link);
                }
                elseif ($json[0] == 400) html_error('Login failed: Email/Password incorrect.');
                elseif ($json[1]['user']['premium'] == null) {
                        $this->changeMesg(lang(300).'<br /><b>Account isn\\\'t premium</b><br />Using it as member.');
                        return $this->FreeDL($link);
                }
        }
        private function PremiumDL($link) {
                $page = $this->GetPage($link, $this->cookie);
                if (strpos($page, '400 Bad Request')) html_error('Link invalid?.');
                if (preg_match('@ocation: (https?://(www\.)?oboom\.com/[^\r\n]+)@i', $page, $redir)) {
                        $page = $this->GetPage(trim($redir[1]), $this->cookie);
                }
                if (!preg_match('@https?://[\w\.]+\.oboom\.com/1\.0/dlh\?ticket=[^\r\n]+@i', $page, $dlink)) {
                       if (!preg_match('@Redirecting to (https?:\/\/api\.oboom\.com\/(1|1\.0)\/dl\?redirect=true&token=[^\r\n]+)@i', $page, $lik))
                        {if (!preg_match('@Redirecting to /#([\w]{8})@i', $page, $redir3)) html_error('Item ID not found.');
                        $page = $this->GetPage('https://www.oboom.com/#' .trim($redir3[1]), $this->cookie);
                        if (!preg_match('@Session : "([^"]+)"@i', $page, $token)) html_error('Token not found.');
                        $page = $this->GetPage('https://api.oboom.com/1/dl', $this->cookie, array('token' => $token[1], 'item' => $redir3[1],),0);
                        list ($header, $page) = array_map('trim', explode("\r\n\r\n", $page, 2));
                        $json = @json_decode($page, true);
                        if (isset($json[0]) && $json[0] == 200) {
                                $link = trim('http://'.$json[1].'/1.0/dlh?ticket='.$json[2]);
                                if (!preg_match('@https?://[\w\.]+\.oboom\.com/1\.0/dlh\?ticket=[^\r\n]+@i', $link, $dlink)) html_error('Error: Download-link not found.');
                        }}
                        elseif (isset($json[0]) && $json[0] != 200) $this->CheckErr($json[0]);
                }
                $this->RedirectDownload($lik[1], urldecode(basename(parse_url($lik[1], PHP_URL_PATH))));
        }
        private function CheckErr($code) {      //Th3-822
                if (is_numeric($code)) {
                        switch ($code) {
                                default: $msg = '*No message for this error*';break;
                                case 400: $msg = 'Bad request. You offered an invalid input parameter. See the attached error message for infos what parameter was invalid/missing and more informations.';break;
                                case 403: $msg = 'Access denied. This includes insufficient user permission to access a resource, the maximal upload filesize or IP conflicts with a token.';break;
                                case 404: $msg = 'Resource not found. The first parameter tells you what resource was not not found, the second (which is optional) why.';break;
                                case 409: $msg = 'Conflict. The requested resource has a conflict with another resource. This usually happens on file system operations like copy or move.';break;
                                case 410: $msg = 'Gone. The resource you requested is no longer available and will not come back The parameters are are the same as with 404.';break;
                                case 413: $msg = 'Request entity too large. The request is handling too much resources at once. This can happen during a cp call.';break;
                                case 421: $msg = 'Connection limit exceeded. You are downloading with too many connections at once or your IP is blocked for (the second parameter tells you how long). This error usually comes when you downloaded too much as a guest or free user.';break;
                                case 500: $msg = 'Internal server error. This should not happen but it may. See the attached error message for more informations.';break;
                                case 503: $msg = 'The service is temporary not available. You may retry later. See the parameters for infos about what is not available.';break;
                                case 507: $msg = 'At least one quota like storage space or item count reached.';break;
                                case 509: $msg = 'Bandwidth limit exceeded. You have to get more traffic to access this resource.';break;
                        }
                        html_error("[Error: $code] $msg.");
                }
        }
        private function Folder($link) {
                $page = $this->GetPage($link);
                if (strpos($page, '400 Bad Request')) html_error('Link invalid?.');
                if (preg_match('@ocation: (https?://(www\.)?oboom\.com/[^\r\n]+)@i', $page, $redir)) {
                        $page = $this->GetPage(trim($redir[1]));
                }
                if (!preg_match('@Redirecting to /#folder/([\w]{8})@i', $page, $FD)) html_error('Item ID not found.');
                $page = $this->GetPage('https://www.oboom.com/#folder/' .trim($FD[1]));
                if (!preg_match('@Session : "([^"]+)"@i', $page, $token)) html_error('Token not found.');
                $page = $this->GetPage('https://api.oboom.com/1/ls', 0, array('token' => $token[1], 'item' => $FD[1],));
                list ($header, $page) = array_map('trim', explode("\r\n\r\n", $page, 2));
                if (strpos($page, '404,"item')) html_error('Empty Folder?.');
                if (!preg_match_all('@","root"\:"\d+","id"\:"(\w+)"@i', $page, $id)) html_error('Empty Folder?.');
                foreach ($id[1] as $id_link) $link_array[] = "https://www.oboom.com/" .$id_link;
                $this->moveToAutoDownloader($link_array);
        }
    private function FreeDL($link) {
                html_error('not support Free Download');
    }
        private function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
        {       //https://github.com/defuse/password-hashing/blob/master/PasswordHash.php
                $algorithm = strtolower($algorithm);
                if(!in_array($algorithm, hash_algos(), true))
                        trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
                if($count <= 0 || $key_length <= 0)
                        trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
                if (function_exists("hash_pbkdf2")) {
                        // The output length is in NIBBLES (4-bits) if $raw_output is false!
                        if (!$raw_output) {
                                $key_length = $key_length * 2;
                        }
                        return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
                }
                $hash_length = strlen(hash($algorithm, "", true));
                $block_count = ceil($key_length / $hash_length);
                $output = "";
                for($i = 1; $i <= $block_count; $i++) {
                        // $i encoded as 4 bytes, big endian.
                        $last = $salt . pack("N", $i);
                        // first iteration
                        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
                        // perform the other $count - 1 iterations
                        for ($j = 1; $j < $count; $j++) {
                                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
                        }
                        $output .= $xorsum;
                }
                if($raw_output)
                        return substr($output, 0, $key_length);
                else
                        return bin2hex(substr($output, 0, $key_length));
        }
}
// [21-4-2014]  Written by giaythuytinh176.
// [17-09-2014] Fixed By Tblogger.
?>