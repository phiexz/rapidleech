<?Php
/**
@author  Developed by mRAza
@link    http://www.wjunction.com/member.php?u=2934
@Dated   09/07/2011
@Modified by: phiexz - 2015
*/

$host = $_SERVER['HTTP_HOST'];
$self = $_SERVER['PHP_SELF'];
$query = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
$url = !empty($query) ? "http://$host$self?$query" : "http://$host$self";
$scripturl =  dirname($url);
?>

<form action="" method="POST">
<table>
  <tr>
  <td align="right">Select File:</td>
  <td>
<?php 
    $path =  dirname(__FILE__)."/../../../files/";
    $listing = exec("ls '$path' | egrep -i '.m4v$|.flv$|.avi$|.wmv$|.mp4$|.f4v$|.mkv$|.3gp$|.3g2$|.asf$|.dat$|.divx$|.mov$|.vob$|.xvid$|.swf$'", $return);
    array_unshift($return, "Select");
    echo"<select name=\"vidfile\">";
    foreach($return as $val=>$file){
        echo "<option value=\"$file\">". $file. "</option>";
    }
    echo"</select>";
?>  
  </td>
  </tr>
  <tr>
  <td align="right">Coulmns :</td>
  <td><select name="cols">
    <option value="1">1</option>
    <option value="2">2</option>
    <option selected="selected" value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    </select>
  </td>
  </tr>
  <tr>
  <td align="right">Rows :</td>
  <td><select name="rows">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option selected="selected" value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    </select>
  </td>
  </tr>
  <tr>
  <td align="right">Image Quality :</td>
  <td><select name="quality">
    <option value="90">90</option>
    </select>
  </td>
  </tr>  
  <tr>
  <td align="right">Precision :</td>
  <td><select name="prec">
    <option value="0">0</option>
    <option value="4">4</option>
    <option value="6">6</option>
    <option value="8">8</option>
    </select>
  </td>
  </tr>
  <tr>
  <td align="right">Distance :</td>
  <td><select name="distance">
    <option value="0">0</option>
    </select>
  </td>
  </tr>
  <tr>
  <td align="right">Time : </td>
  <td><input type="text" name="time" />
  </td>
  </tr>
  <tr>
  <td align="right">Additional Text </td>
  <td><input type="text" name="additional" />
  </td>
  </tr>  
  <tr>
  <td align="right">Text Size : </td>
  <td><select name="textsize">
    <option value="10">10</option>
    </select>
  </td>
  </tr>
  <tr>
  <td align="right">Font for text : </td>
  <td>
<?php 
$fontpath =  dirname(__FILE__)."/";

  $listing = exec("ls '$fontpath' | egrep -i '.ttf$'", $freturn);
    array_unshift($return, "Select");
  echo"<select name=\"font\">";  
    foreach($freturn as $val=>$ffile){
    echo "<option value=$ffile>". $ffile. "</option>";
    }
  echo"</select>";
?>    
  </td>
  </tr>
  <tr>
  <td align="right">Text Color:  </td>
  <td><input class="color" name="textcolor" value="000000">
  </td>
  </tr>
  <tr>
  <td align="right">Background Color:  </td>
  <td><input class="color" name="bgcolor" value="FFFFFF">
  </td>
  </tr>
  <tr>
  <td align="right"></td>
  <td><input type="checkbox" name="removeinfo">Remove Video Info.
  </td>
  </tr>
  <tr>
  <td align="right"></td>
  <td><input type="checkbox" name="removetime">Remove Time.
  </td>
  </tr>
  <tr>
  <td align="right"></td>
  <td><input type="checkbox" name="savethumb">Save thumbnails in separated file.
  </td>
  </tr>
  <tr>
  <td align="right"></td>
  <td><input type="checkbox" name="saveinfotxt">Save video info in a text file.
  </td>
  </tr>
  <tr>
  <td align="right"></td>
  <td><input type="submit" name="mtn-submit" value="Submit" />
  </td>
  </tr>
</table>
</form>

<?php
if(isset($_POST['mtn-submit'])) {

$path =  dirname(__FILE__)."/";
$pathz =  dirname(__FILE__)."/../../../files/";
$file = $_POST['vidfile'];
$cols = $_POST['cols'];
$rows = $_POST['rows'];
$quality = $_POST['quality'];
if(!empty($_POST['prec'])){
$prec = ' -D '. $_POST['prec'];
}else {
$prec = '';
}
$distance = $_POST['distance'];
if(!empty($_POST['time'])){
$time = ' -s ' . $_POST['time'];
}else {
$time = '';
}
if(!empty($_POST['additional'])){
$additional = ' -T '. $_POST['additional'];
}else {
$additional = '';
}
if(!empty($_POST['textsize'])){
$textsize = $_POST['textsize'];
}else {
$textsize = 8;
}
$font = $_POST['font'];

if(!empty($_POST['textcolor'])){
$textcolor = ' -F '.$_POST['textcolor'].':'.$textsize.'';
}else {
$textcolor = '';
}
if(!empty($_POST['bgcolor'])){
$bgcolor = ' -k '.$_POST['bgcolor'];
}else {
$bgcolor = '';
}
$removeinfo = $_POST['removeinfo'];

if(!empty($_POST['removeinfo'])){
$removeinfo = ' -i ';
}else {
$removeinfo = '';
}
if(!empty($_POST['removetime'])){
$removetime = ' -t ';
}else {
$removetime = '';
}
$savethumb= $_POST['savethumb'];

if(!empty($_POST['saveinfotxt'])){
$saveinfotxt = "-N _info.txt";
}else {
$saveinfotxt = '';
}
$page = exec( "'" . $path . "mtn' -c $cols -r $rows $time $additional -j $quality $bgcolor $prec $saveinfotxt $removetime $textcolor -f " . $path . "$font $removeinfo -O files/ \"files/" . $file . "\" " );
echo "'" . $path . "mtn' -c $cols -r $rows $time $additional -j $quality $bgcolor $prec $saveinfotxt $removetime $textcolor -f " . $path . "$font $removeinfo -O files/ \"files/" . $file . "\" " ;
//echo $pathz . "\"" . $file . "\" ";
}
?>