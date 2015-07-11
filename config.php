<?
//Debug mode on/off
$debug=false;

//Mysql connection
$link = mysql_connect("localhost", "dict", "bazpas")or die("Could not connect : " . mysql_error());
mysql_select_db("dict",$link) or die("Could not select database");

$config['host']='http://dict';
?>