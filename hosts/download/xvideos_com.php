<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}
class xvideos_com extends DownloadClass {
    public function Download($link) {        
        $page = $this->GetPage($link);  
        if (preg_match('#<title>(.*) - XVIDEOS.COM</title>#', $page, $FileName))
        if (preg_match("#flv_url=(http(.*))\&amp#", $page, $dl))        
        $this->RedirectDownload(urldecode($dl[1]), $FileName[1], $link);
        exit();
    }
}
// Create by IndoLeech.Com 25-05-2012 Need more plugin support@indoleech.com
?>