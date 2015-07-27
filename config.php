<?
//Debug mode on/off
$debug=$_COOKIE['debug']||false;

//Simple mode
$simple=false;

//Mysql connection
//$link = mysql_connect("localhost", "dict_v1", "bazpas")or die("Could not connect : ".mysql_error());
//mysql_select_db("dict_v1", $link) or die("Could not select database");
$mysqli=mysqli_connect("localhost", "dict_v1", "bazpas", "dict_v1") or die("Could not connect : ".mysqli_error());
$config['path']='http://worldofwords.ru';
?>