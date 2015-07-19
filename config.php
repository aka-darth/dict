<?
//Debug mode on/off
$debug=false;
//Simple mode
$simple=false;

//Mysql connection
$link = mysql_connect("mysql.hostinger.ru", "u115595049_dict", "bazpas")or die("Could not connect : ".mysql_error());
mysql_select_db("u115595049_dict",$link) or die("Could not select database");
$config['path']='http://shcoding.esy.es/dict';
?>