<?php
if (!defined('RAPIDLEECH')) {
    require_once ("index.html");
    exit ();
}
 
class xvideos_com extends DownloadClass {
 
    public function Download($link) {        
        $page = $this->GetPage($link); 
        if (preg_match('#<title>(.*) - Solidfiles</title>#', $page, $FileName))
          html_error($FileName)
          //if (preg_match("#flv_url=(http(.*))\&amp#", $page, $dl))        
          //$this->RedirectDownload(urldecode($dl[1]), $FileName[1], $link);
        exit();
    }
}
// Create by phiexz 20-01-2014
?>