﻿<?error_reporting(E_ALL);
include "login.php";
//Здесь надо закрыть дыру в безопасности и сделать вывод под аякс
$query=mysqli_query($mysqli,"SELECT * FROM dt_W_".$user["id"]." WHERE id='".$_GET["id"]."'");
$word=mysqli_fetch_assoc($query);
if($debug) echo "Get user:".$user["id"]."<br>";
if($debug) echo "Get word:".$_GET["id"]."<br>";
if($debug) echo mysqli_num_rows($query);
if($debug) print_r($word);

if($_GET['id']==$word['id']){
	//убрать у всех из таргет
	if(strpos($word['target'],",")){
		$targets=explode($word['target']);
		foreach($targets as $target){
			$target_word=mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT target FROM dt_W_".$user['id']." WHERE id=".$target));
			if(strpos($target_word['target'],",")){
				$target_word_targets=explode(",",$target_word['target']);
				if($debug) echo "<br>Before:";
				print_r($target_word_targets);
				if($key=array_search($word['id'],$target_word_targets)){
					unset($target_word_targets[$key]);
				}
				if($debug) echo "<br>After:";
				print_r($target_word_targets);
				mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".implode(",",$target_word_targets)."' WHERE id=".$target);
			}else{
				mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='' WHERE id=".$target);
				if($debug) echo mysqli_error($mysqli);
			}
		}
	}else{
		$target_word=mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT target FROM dt_W_".$user['id']." WHERE id=".$word['target']));
		if(strpos($target_word['target'],",")){
			$target_word_targets=explode(",",$target_word['target']);
			if($debug) echo "<br>Before:";
			print_r($target_word_targets);
			if($key=array_search($word['id'],$target_word_targets)){
				unset($target_word_targets[$key]);
				if($debug) echo "<br>".$key;
			}else{
				if($debug) echo "<br>Not finded";
			}
			if($debug) echo "<br>After:";
			print_r($target_word_targets);
			mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".implode(",",$target_word_targets)."' WHERE id=".$word['target']);
		}else{
			mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='' WHERE id=".$word['target']);
			if($debug) echo mysqli_error($mysqli);
		}
	}
	//удалить
	mysqli_query($mysqli,"DELETE FROM dt_W_".$user['id']." WHERE id=".$word['id']);
	if($debug) echo mysqli_error($mysqli);
}else{
	if($debug) echo "Произошла подозрительная ошибка. Не ломайте мой сервис! Сообщите мне. <br>";
}
if($debug){
?><br>
<input type="button" onclick="document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>'" autofocus value="Продолжить">
<?}else{?>
<script>
	document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>';
</script>
<?}?>