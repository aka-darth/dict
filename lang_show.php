<?include "../mysql.php";
include "login.php";
//Здесь надо закрыть дыру в безопасности и сделать вывод под аякс
$o=mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT showlang FROM dt_lang_".$user['id']." WHERE id=".$_GET['id']));
if($o['showlang']){
	$o=0;
}else{
	$o=1;
}
mysqli_query($mysqli,"UPDATE
 dt_lang_".$user['id']." 
SET showlang=".$o."
 WHERE
 id=".$_GET['id']);
echo mysqli_error($mysqli);
echo ",ok?";
?>