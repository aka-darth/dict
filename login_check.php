<?include "../mysql.php";
$user=mysql_fetch_assoc(mysql_query("SELECT id FROM dt_users WHERE login LIKE '".$_GET['login']."'"));
if($user['id']){
	echo $user['id'];
}else{
	echo "null";
}
//echo mysql_error();
?>