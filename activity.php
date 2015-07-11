<?include "../mysql.php";
include "login.php";
//Здесь надо закрыть дыру в безопасности и сделать вывод под аякс

if($_GET['id']){
	if($_GET['enabled']=="1"){
		mysql_query("UPDATE dt_W_".$user['id']." SET status=0 WHERE id=".$_GET['id']);
	}else{
		mysql_query("UPDATE dt_W_".$user['id']." SET status=4 WHERE id=".$_GET['id']);
	}
	echo mysql_error();
	echo "ok";
}else{
	echo "Where is id?<br>";
}
?>