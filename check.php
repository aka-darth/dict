<?
$page="check";
include "top.php";
$i=0;
$end_time=0;
$total=$_COOKIE['total'] or 0;
$right=$_COOKIE['right'] or 0;



while($_POST["id".$i]){
	$total++;
	$may_add=false;
	//Берем исходное слово
	$query=mysqli_query($mysqli,"SELECT id,word,target,status,attempts,success,lang FROM dt_W_".$user["id"]." WHERE id=".$_POST["id".$i]);
	$keyword=mysqli_fetch_assoc($query);
	$keytargets=explode(",",$keyword['target']);
	mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET attempts=".($keyword['attempts']+1).", last_attempt=NOW() WHERE id=".$keyword["id"]);
	$success=$keyword['success'];
	$now=$keyword['status'];
	$lang=$keyword['lang'];
	$keyword_id=$keyword['id'];
	$keyword=$keyword['word'];
	
	
	if(!$_POST['word'.$i]){//Нет перевода
		echo "<br><p style='color:#faa;font-size:20px;font-weight:bold;'>".$keyword." : Пустое значение!</p>";
		$query=mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET status=0 WHERE id=".$keyword_id);
		echo "<ul class='transes'>";
		foreach($keytargets as $target){
			$query=mysqli_query($mysqli,"SELECT word FROM dt_W_".$user["id"]." WHERE id=".$target);
			$word=mysqli_fetch_assoc($query);
			echo "<li>".$word['word']."</li>";
		}
		echo "</ul>";
		$end_time+=5;
		$i++;
		continue;
	}
	//Поиск пользовательского перевода 
	$query="SELECT * FROM dt_W_".$user["id"]." WHERE word LIKE '".strtolower($_POST['word'.$i])."'";
	if($_POST['to'] or $_POST['to']==="0"){
		$query.=" AND lang=".$_POST['to'];
	}
	$query=mysqli_query($mysqli,$query);
	if($word=mysqli_fetch_assoc($query)){
		$targets=explode(",",$word['target']);
		if(in_array($_POST["id".$i],$targets)){//Правильный перевод
			$right++;
			echo "<p style='color:#a99;font-size:16px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
			mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET success=".($success+1).",status=".($now+1)." WHERE id=".$_POST["id".$i]);
			if(count($keytargets)>1){
				$end_time+=3;
				echo "<ul class='transes'>";
				foreach($keytargets as $target){
					$query=mysqli_query($mysqli,"SELECT word FROM dt_W_".$user["id"]." WHERE id=".$target);
					$word=mysqli_fetch_assoc($query);
					echo "<li>".$word['word']."</li>";
				}
				echo "</ul>";
			}
		}else{//Неравильный перевод, введено неправильное слово,которое однако есть в базе..
			echo "<p style='color:#f90;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
			$end_time+=3;
			mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET status=0 WHERE id=".$keyword_id);
			echo "Доступные варианты - <br><ul class='transes'>";
			foreach($keytargets as $target){
				$query=mysqli_query($mysqli,"SELECT word FROM dt_W_".$user["id"]." WHERE id=".$target);
				$word=mysqli_fetch_assoc($query);
				echo "<li>".$word['word']."</li>";
			}
			echo "</ul>";
			$may_add=true;
		}
	}else{//Неравильный перевод, введеного слова нет в базе (тут искать опечатки и транслит)

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
			if($op){
				echo "Опечатка? L:".$op;
			}


		$trans=array(
			'`'=>'ё',
			'q'=>'й',
			'w'=>'ц',
			'e'=>'у',
			'r'=>'к',
			't'=>'е',
			'y'=>'н',
			'u'=>'г',
			'i'=>'ш',
			'o'=>'щ',
			'p'=>'щ',
			'['=>'з',
			']'=>'х',
			'a'=>'ъ',
			's'=>'ф',
			'd'=>'в',
			'f'=>'а',
			'g'=>'п',
			'h'=>'р',
			'j'=>'о',
			'k'=>'л',
			'l'=>'д',
			';'=>'ж',
			'\\'=>'\\',
			'"'=>'Э',
			'z'=>'я',
			'x'=>'ч',
			'c'=>'с',
			'v'=>'м',
			'b'=>'и',
			'n'=>'т',
			'm'=>'ь',
			','=>'б',
			'.'=>'ю',
			'/'=>'.',
			'~'=>'ё',
			'{'=>'х',
			'}'=>'ъ',
			':'=>'Ж',
			'"'=>'Э',
			'<'=>'Ю',
			'>'=>'Б',
			'?'=>','
		);
		
		switch($lang){
			case 2://En
			break;
			case 1://Ru
				$trans=array_flip($trans);			
			break;
			default:echo $lang;
		}
		$translit=strtr(strtolower($_POST['word'.$i]),$trans);
		$qu="SELECT * FROM dt_W_".$user["id"]." WHERE word LIKE '".$translit."'";
		if($_POST['to'] or $_POST['to']==="0"){
			$qu.=" AND lang=".$_POST['to'];
		}
		$qu=mysqli_query($mysqli,$qu);
		if($word=mysqli_fetch_assoc($qu)){
			$targets=explode(",",$word['target']);
			if(in_array($_POST["id".$i],$targets)){//Правильный перевод
				$right++;
				echo "<br>Распознана неправильная раскладка.";
				echo "<p style='color:#f90;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]." > ".$translit."</p>";
				mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET success=".($success+1).",status=".($now+1)." WHERE id=".$_POST["id".$i]);
				if(count($keytargets)>1){
					$end_time+=3;
					echo "Доступные варианты - <br><ul class='transes'>";
					foreach($keytargets as $target){
						$query=mysqli_query($mysqli,"SELECT word FROM dt_W_".$user["id"]." WHERE id=".$target);
						$word=mysqli_fetch_assoc($query);
						echo "<li>".$word['word']."</li>";
					}
					echo "</ul>";
				}
			}else{//Неравильный перевод, введено неправильное слово,которое однако есть в базе..
				echo "<p style='color:#f00;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
				$end_time+=3;
				mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET status=0 WHERE id=".$keyword["id"]);
				echo "Доступные варианты - <br><ul class='transes'>";
				foreach($keytargets as $target){
					$query=mysqli_query($mysqli,"SELECT word FROM dt_W_".$user["id"]." WHERE id=".$target);
					$word=mysqli_fetch_assoc($query);
					echo "<li>".$word['word']."</li>";
				}
				echo "</ul>";
				$may_add=true;
			}
		}else{//Самый провальный вариант
			echo "<p style='color:#f00;font-size:20px;font-weight:bold;'>".$keyword." : ".$_POST['word'.$i]."</p>";
			$end_time+=3;
			echo "Доступные варианты - <br><ul class='transes'>";
			mysqli_query($mysqli,"UPDATE dt_W_".$user["id"]." SET status=0 WHERE id=".$keyword_id);
			foreach($keytargets as $target){
				$query=mysqli_query($mysqli,"SELECT word FROM dt_W_".$user["id"]." WHERE id=".$target);
				$word=mysqli_fetch_assoc($query);
				echo "<li>".$word['word']."</li>";
			}
			echo "</ul>";
			$may_add=true;
		}
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
<input type="button" value="Остаться" onclick="clearTimeout(go);document.getElementById('timer').firstChild.nodeValue='';this.style.display='none';">
<input type="button" onclick="document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>'" autofocus value="Дальше">
<script>
	end_time=<?=$end_time;?>;
	time=0;
	document.cookie="total=<?echo $total;?>;";
	document.cookie="right=<?echo $right;?>;";
	go=setInterval(function(){
		time++;
		if(time>end_time){
			document.location.href='<?if($total){echo $_SERVER['HTTP_REFERER'];}else{echo $config['path']."/test.php";}?>';
		}else{
			document.getElementById('timer').firstChild.nodeValue='Страница закроется через '+(end_time-time)+' c.';
		}
	},1000);
	function choose_lang(){
		
	}
</script>
</body>
</html>