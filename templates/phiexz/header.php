<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="author" content="blacklizt">
<meta name="robots" content="all">
<meta name="description" content="PX Rapidleech">
<meta name="keywords" content="PX phiexz rapidleech">
<link rel="stylesheet" href="templates/phiexz/styles/rl_style_pm.css">
<link rel="stylesheet" href="templates/phiexz/styles/bootstrap_custom.css">
<title><?php
if (!isset($nn)) $nn = "\r\n";
if (!isset($page_title)) {
	echo 'Rapidleech v2 rev. '.$rev_num;
} else {
	echo htmlspecialchars($page_title);
}
?></title>
<script type="text/javascript">
/* <![CDATA[ */
var php_js_strings = [];
php_js_strings[87] = " <?php echo lang(87); ?>";
pic1= new Image(); 
pic1.src="templates/phiexz/images/ajax-loading.gif";
/* ]]> */
</script>
<script src="classes/js.js"></script>
<script src="templates/phiexz/js/serverstats.js"></script>
<?php
if ($options['ajax_refresh']) { echo '
<script type="text/javascript">
  $(document).ready(function(){
    setInterval(ServerStatus, 300 * 1e3);
  });
</script>'.$nn; }
if ($options['flist_sort']) { echo '<script src="classes/sorttable.js"></script>'.$nn; }
?>
<script type="text/javascript">function toggle(b){var a=document.getElementById(b);if(a.style.display=="none"){a.style.display="block"}else{a.style.display="none"}};</script>
</head>
<body>
<header><div id="header"><a class="header" href="<?php echo $_SERVER['SERVER_NAME']; ?>">PX Rapidleech</a></div></header><br />
