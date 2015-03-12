<?php

if (!defined('RAPIDLEECH')) {
   require_once('index.html');
   exit;
}

class zippyshare_com extends DownloadClass {
   public $link;
   private $page, $cookie, $fid;
   public function Download($link) {
//      echo "FIK"; exit;
      
//      print $link;exit;
      
      $this->link = $link;
//      $this->cookie = array('ziplocale' => 'en');
      $this->cookie = array();


      if (!preg_match('@https?://(?:[\w\-]+\.)*zippyshare\.com/\w/(\d+)@i', $this->link, $this->fid)) html_error('File ID not found at link. Invalid link?');
      $this->fid = $this->fid[1];

      if (!empty($_POST['step'])) switch ($_POST['step']) {
         case '1': return $this->CheckCaptcha();
         case '2': return $this->GetDecodedLink();
      }

      $this->page = $this->GetPage($this->link, $this->cookie);
//print $this->page;exit;
      is_present($this->page, '>File does not exist on this server<', 'File does not exist.');
      is_present($this->page, '>File has expired and does not exist anymore on this server', 'File does not exist.');
      $a = $this->page;
      $this->cookie = GetCookiesArr($this->page, $this->cookie);      
      // print_r($this->cookie);exit;
//print $this->page;exit;
      //if (($pos = stripos($this->page, 'getElementById(\'dlbutton\').href')) !== false || ($pos = stripos($this->page, 'getElementById("dlbutton").href')) !== false) return $this->GetJSEncodedLink();
      if ($this->findJS($a)) return $this->GetJSEncodedLink();
      else return $this->GetCaptcha();
   }

   private function GetDecodedLink() {
      if (empty($_POST['dllink']) || ($dlink = parse_url($_POST['dllink'])) == false || empty($dlink['path'])) html_error('Empty decoded link field.');
      $this->cookie = urldecode($_POST['cookie']);

      $dlink = 'http://' . parse_url($this->link, PHP_URL_HOST) . $dlink['path'] . (!empty($dlink['query']) ? '?' . $dlink['query'] : '');
      $fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
      $this->RedirectDownload($dlink, $fname, $this->cookie);
   }

   private function findJS($a) {
      if (!preg_match_all('@<script(?:\s[^>]*)?>([^<]+)</script>@i', $a, $scripts)) {
//         print "A";
//         print $a;//exit;
          html_error('No inline JS found at page.');
      }
//      print $this->page;exit;
   //   foreach ($scripts[1] as $script) if (preg_match('@\.getElementById\(\s*(?:(\'|\")(?i)(?:dlbutton|fimage)(?-i)\1|([\$_A-Za-z][\$\w]*))\)\.href\s*=\s*[\'\"](?:https?://(?:[\w\-]+\.)*zippyshare\.com)?/d/'.$this->fid.'@', $script, $match)) {
      foreach ($scripts[1] as $script) if (preg_match('@fl1x|var a.? @', $script, $match)) {
         if (!empty($match[2])) $this->vname = $match[2];
         $this->script = $script;
         return true;
      }
      return false;
   }

   private function GetJSEncodedLink() {
      
   
//      $this->script = rtrim(str_replace(array(').href', "'dlbutton'", '"dlbutton"', '    '), array(').value', "'T8_dllink'", '"T8_dllink"', "\t\t"), $this->script));
      if (empty($this->script)) html_error('Error while getting js code.');
      preg_match("/(ww.*?)\//", $this->link, $host);
      $host = "http://".$host[1];
      $a = $this->script;
$m = preg_match("/fl1x (.*)/", $a, $matches);
$mm = preg_match("/var a.? = ((?:.|\n|\r])+)/", $a, $matches2);
// print $m;print $mm;
$m = preg_match("/fl1x (.*)/", $a, $matches);
$mm = preg_match("/var a.? = ((?:.|\n|\r])+)/", $a, $matches2);
// print $m;print $mm;
$dlink = "NOTHING";
$filename = "NOTHING";
if ($m > 0) {
   preg_match("/\"([^\"]*?)\";/", $matches[1], $filename);$filename = $filename[1];
   $filename = urldecode($filename);
   $j = preg_match_all("/([0-9]+)/", $matches[1], $matchess);
   $matchess = $matchess[1];
   $dlink = "/d/". $matchess[0] . "/" . ($matchess[1] % $matchess[2] + $matchess[3] % $matchess[4] . "$filename");
} else if ($mm > 0) {
   $j = preg_match_all("/([0-9][0-9%]*)/", $matches2[1], $m);
   $m = $m[0];
   // print_r($m);

   $a = $m[0];
   $omg = eval("return ".$m[1].";");
   $b = eval("return ".$m[2].";") * $omg;
   $e = ($a + 3) % $b + 3;
   // $b = eval("return 1+1;");   
   preg_match_all('/\/d\/.*?".*?"(.*?)"/', $matches2[0] ,$mm);
   preg_match('/d\/(\d+)/', $matches2[0] ,$t);   
   // print_r($mm);
   // print $mm[1][0];
   $filename = substr(urldecode( $mm[1][0] ),1);
   $dlink = "/d/". $t[1] . "/" . ($b + 18) . urldecode($mm[1][0]);      
   
   // print $matches2[1];
   // print $dlink;
   // print exit;
   // print_r($mm[0][1]);
   // print_r($mm);
}// print $this->link;$host = "http://".parse_url($this->link, PHP_URL_HOST);
else {
   // exit;
echo("</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ */\n\tvar T8 = true;\n\ttry {{$this->script}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode link: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=zs_dcode]').submit();\", 300); // 300 Âµs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>");
      exit;
}
// print_r($this->cookie);
//echo $this->cookie;exit;
   // echo "$a \n $dlink";
    // echo $host.$dlink;      print "\n<br>";
    // echo $filename;exit;
      $this->RedirectDownload($host.$dlink, $filename, $this->cookie);
      return;

      $T8 = '';
      if (preg_match_all('@getElementById[\s\t]*\([\s\t]*[\"\']([a-z][\w\.\-]*)[\"\'][\s\t]*\)@i', $this->script, $ids) && count($ids[0]) > 1) foreach (array_unique($ids[1]) as $id) {
         if ($id == 'T8_dllink') continue;
         if (!preg_match("@<([a-z][a-z\d]*)\s*(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:\s*id\s*=[\"\']{$id}[\"\']\s*)(?:\w+\s*=\s*[\"\'][^\"\'<>]*[\"\']\s*)*(?:(\s*>[^<>]*</\1)|(/)?[\s]*>)@i", $this->page, $tag)) break; // If it doesn't found the tag the decode will fail, im not sure if break or continue...
         $T8 .= (!empty($tag[2]) ? $tag[0].'>' : (empty($tag[3]) ? $tag[0].'</'.$tag[1].'>' : $tag[0]));
      }
      $this->script = preg_replace('@^\s*(?:function\s+[\$_A-Za-z][\$\w]*|(?:var\s+)?[\$_A-Za-z][\$\w]*\s*=\s*function)\s*\([^)]*\)\s*\{\s*[\$_A-Za-z][\$\w]*\.\w+\s*\(\s*[\'\"]\w+[\'\"]\s*\)\.value\s*=\s*[\'\"][^\'\"][\'\"]*\s*;\s*\};?@', '', $this->script, 1);
      if (preg_match('@^\s*function\s+([\$_A-Za-z][\$\w]*)\s*\(@i', $this->script, $funcName) || preg_match('@^\s*(?:var\s+)?([\$_A-Za-z][\$\w]*)\s*=\s*function\s*\(@i', $this->script, $funcName)) $this->script .= "\n\t\t{$funcName[1]}();";

      $data = $this->DefaultParamArr($this->link, $this->cookie);
      $data['step'] = '2';
      $data['dllink'] = '';
      echo "\n<div style='display:none;'>$T8</div>\n<form name='zs_dcode' action='{$GLOBALS['PHP_SELF']}' method='POST'><br />\n";
      foreach ($data as $name => $input) echo "<input type='hidden' name='$name' id='T8_$name' value='$input' />\n";
      echo("</form>\n<span id='T8_emsg' class='htmlerror' style='text-align:center;display:none;'></span>\n<noscript><span class='htmlerror'><b>Sorry, this code needs JavaScript enabled to work.</b></span></noscript>\n<script type='text/javascript'>/* <![CDATA[ */\n\tvar T8 = true;\n\ttry {{$this->script}\n\t} catch(e) {\n\t\t$('#T8_emsg').html('<b>Cannot decode link: ['+e.name+'] '+e.message+'</b>').show();\n\t\tT8 = false;\n\t}\n\tif (T8) window.setTimeout(\"$('form[name=zs_dcode]').submit();\", 300); // 300 Âµs to make sure that the value was decoded and added.\n/* ]]> */</script>\n\n</body>\n</html>");
      exit;
   }

   private function GetCaptcha() {
      if (!preg_match('@/d/\d+/(\d+)/[^\r\n\t\'\"<>\;]+@i', $this->page, $dlpath)) html_error('Download Link Not Found.');
      if (!preg_match('@Recaptcha\.create[\s\t]*\([\s\t]*\"([\w\.\-]+)\"@i', $this->page, $cpid)) html_error('reCAPTCHA Not Found.');
      //if (!preg_match('@\Wshortencode[\s\t]*:[\s\t]*\'?(\d+)\'?@i', $this->page, $short)) html_error('Captcha Data Not Found.');

      $data = $this->DefaultParamArr($this->link, $this->cookie);
      $data['step'] = '1';
      $data['dlpath'] = urlencode($dlpath[0]);
      $data['shortencode'] = urlencode($dlpath[1]);

      $this->reCAPTCHA($cpid[1], $data);
   }

   private function CheckCaptcha() {
      if (empty($_POST['recaptcha_response_field'])) html_error('You didn\'t enter the image verification code.');
      $host = 'http://' . parse_url($this->link, PHP_URL_HOST);
      $this->cookie = urldecode($_POST['cookie']);

      $post = array();
      $post['challenge'] = $_POST['recaptcha_challenge_field'];
      $post['response'] = $_POST['recaptcha_response_field'];
      $post['shortencode'] = $_POST['shortencode'];

      $page = $this->GetPage($host . '/rest/captcha/test', $this->cookie, $post, $this->link . "\r\nX-Requested-With: XMLHttpRequest");
      $body = strtolower(trim(substr($page, strpos($page, "\r\n\r\n"))));

      if ($body == 'false') html_error('Error: Wrong CAPTCHA Entered.');
      elseif ($body != 'true') html_error('Unknown Reply from Server.');

      $dlink = $host . urldecode($_POST['dlpath']);
      $fname = urldecode(basename(parse_url($dlink, PHP_URL_PATH)));
      $this->RedirectDownload($dlink, $fname, $this->cookie);
   }
}

// [24-11-2012]  Written by Th3-822. (Only for rev43 :D)
// [05-2-2013]  Added support for links that need user-side decoding of the link. - Th3-822
// [04-3-2013]  Fixed File doesn't exists error msg... - Th3-822
// [25-3-2014]  Fixed link decoder function. - Th3-822
// [20-9-2014]  Quick fix to decoder function. - Th3-822
?>