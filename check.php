<?
$page="check";
include "top.php";
$i=0;
$end_time=0;
$total=$_COOKIE['total'] or 0;
$right=$_COOKIE['right'] or 0;
while($_POST['id'.$i]){
	$total++;
	$may_add=false;
	$query=mysql_query("SELECT id,word,target,status,attempts,success,lang FROM dt_W_".$user['id']." WHERE id=".$_POST['id'.$i]);
	$keyword=mysql_fetch_assoc($query);
	$keytargets=explode(",",$keyword['target']);	
	mysql_query("UPDATE dt_W_".$user['id']." SET attempts=".($keyword['attempts']+1).",last_attempt=NOW() WHERE id=".$keyword['id']);
	$success=$keyword['success'];
	$now=$keyword['status'];
	$lang=$keyword['lang'];
	$keyword=$keyword['word'];//Слово,которое переводили
	if(!$_POST['word'.$i]){//Нет перевода
		echo "<br><p style='color:#f00;font-size:20px;font-weight:bold;'>".$keyword." : Пустое значение!</p>";
		$query=mysql_query("UPDATE dt_W_".$user['id']." SET status=0 WHERE id=".$keyword['id']);
		echo "Доступные варианты - <br><ul class='transes'>";
		foreach($keytargets as $target){
			$query=mysql_query("SELECT word FROM dt_W_".$user['id']." WHERE id=".$target);color:#f00;
			$word=mysql_fetch_assoc($query);
			echo "<li>".$word['word']."</li>";
		}
		echo "</ul>";
		$end_time+=5;
		$i++;
		continue;
	}
	$query="SELECT * FROM dt_W_".$user['id']." WHERE word LIKE '".$_POST['word'.$i]."'";
	if($_POST['to'] or $_POST['to']==="0"){
		$query.=" AND lang=".$_POST['to'];
	}
	$query=mysql_query($query);
	if($word=mysql_fetch_assoc($query)){
		$targets=explode(",",$word['target']);
		if(in_array($_POST['id'.$i],$targets)){//Правильный перевод
			$right++;
			echo "<br><p style='color:#f90;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
			mysql_query("UPDATE dt_W_".$user['id']." SET success=".($success+1).",status=".($now+1)." WHERE id=".$_POST['id'.$i]);
			if(count($keytargets)>1){
				$end_time+=3;
				echo "Доступные варианты - <br><ul class='transes'>";
				foreach($keytargets as $target){
					$query=mysql_query("SELECT word FROM dt_W_".$user['id']." WHERE id=".$target);
					$word=mysql_fetch_assoc($query);
					echo "<li>".$word['word']."</li>";
				}
				echo "</ul>";
			}
		}else{//Неравильный перевод
			$op=false;
			/*
			foreach($targets as $target){
				$t=levenshtein($_POST['word'.$i],$word['word']);
				echo $word['word'];
				echo $target." L:".$t."<br/>";
				if($t<50){
					$op=$t;
					break;
				}
			}*/
			echo "<br><p style='color:#f00;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
			if($op){
				echo "Опечатка? L:".$op;
			}
			$end_time+=3;
			mysql_query("UPDATE dt_W_".$user['id']." SET status=0 WHERE id=".$keyword['id']);
			echo "Доступные варианты - <br><ul class='transes'>";
			foreach($keytargets as $target){
				$query=mysql_query("SELECT word FROM dt_W_".$user['id']." WHERE id=".$target);
				$word=mysql_fetch_assoc($query);
				echo "<li>".$word['word']."</li>";
			}
			echo "</ul>";
			$may_add=true;
		}
	}else{
		echo "<br><p style='color:#f00;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
		$end_time+=3;
		echo "Доступные варианты - <br><ul class='transes'>";
		mysql_query("UPDATE dt_W_".$user['id']." SET status=0 WHERE id=".$keyword['id']);
		foreach($keytargets as $target){
			$query=mysql_query("SELECT word FROM dt_W_".$user['id']." WHERE id=".$target);
			$word=mysql_fetch_assoc($query);
			echo "<li>".$word['word']."</li>";
		}
		echo "</ul>";
		$may_add=true;
	}
	if($may_add){?>
		<form action="add.php" method="post">
			<input type="hidden" name="lang1" value="<?echo $lang;?>">
			<input type="hidden" name="lang2" value="<?echo $_POST['to'];?>">
			<input type="hidden" name="word1" value="<?echo $keyword;?>">
			<input type="hidden" name="word2" value="<?echo $_POST['word'.$i];?>">
			<input type="submit" value="Добавить эту пару"><br>
		</form><?
		$end_time+=3;
	}
	$i++;
}?>
<span id="timer"> </span><br>
<input type="button" value="Остаться" onclick="clearTimeout(go);document.getElementById('timer').firstChild.nodeValue='';">
<input type="button" onclick="document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>'" autofocus value="Продолжить">
<script>
	end_time=<?=$end_time;?>;
	time=0;
	document.cookie="total=<?echo $total;?>";
	document.cookie="right=<?echo $right;?>";
	go=setInterval(function(){
		time++;
		if(time>end_time){
			document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>';
		}else{
			document.getElementById('timer').firstChild.nodeValue='Страница закроется через '+(end_time-time)+' c.';
		}
	},1000)
</script>
</body>
</html>