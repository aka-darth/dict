<?include "../mysql.php";
include "login.php";
//Здесь надо закрыть дыру в безопасности и сделать вывод под аякс
$o=mysql_fetch_assoc(mysql_query("SELECT showlang FROM dt_lang_".$user['id']." WHERE id=".$_GET['id']));
if($o['showlang']){
	$o=0;
}else{
	$o=1;
}
mysql_query("UPDATE
 dt_lang_".$user['id']." 
SET showlang=".$o."
 WHERE
 id=".$_GET['id']);
echo mysql_error();
echo ",ok?";
?>