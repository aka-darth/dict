<?
include "../mysql.php";
include "login.php";
header('Content-type: application/json; charset=utf-8');
//Здесь надо закрыть дыру в безопасности и сделать вывод под аякс

//print_r($_POST);
//echo "<hr>";

$old_word=mysql_fetch_assoc(mysql_query("SELECT word,lang,target FROM dt_W_".$user['id']." WHERE id=".intval($_POST['id'])));

$main_query="UPDATE dt_W_".$user['id']." SET ";
if($_POST['word']!=$old_word['word']){
	$main_query.=" word='".$_POST['word']."',";
}


$new_targets=array();
$old_targets=explode(",",$old_word['target']);

//print_r($old_targets);
//echo "<hr>";

$i=0;
while($_POST['trans'.$i]){
	if($_POST['id'.$i]=="new"){
		$query=mysql_query("SELECT id,target FROM dt_W_".$user['id']." WHERE word LIKE '".$_POST['trans'.$i]."'");
		if($finded_word=mysql_fetch_assoc($query)){
			if($key=array_search($finded_word['id'],$old_targets)){
				$new_targets[]=$old_targets[$key];
				unset($old_targets[$key]);
			}else{
				//echo "In base but not in this word";
				if(strlen($finded_word['target'])){
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$finded_word['target'].",".$_POST['id']."' WHERE id=".$finded_word['id']);
				}else{
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$_POST['id']."' WHERE id=".$finded_word['id']);
				}
				$new_targets[]=$finded_word['id'];
			}
		}else{
			//echo $_POST['trans'.$i]." нет в базе!<br>";
			$query=mysql_query("INSERT INTO dt_W_".$user['id']." VALUES ('','".$_POST['trans'.$i]."','".$_POST['lang'.$i]."','0','','0','0','".$_POST['id']."')");
			$new_targets[]=mysql_insert_id();
			//echo "Added as ".$new_targets[count($new_targets)-1]."<br>";
		}
	}else{
		$key=array_search($_POST['id'.$i],$old_targets);
		if($key!==false){
			$new_targets[]=$old_targets[$key];
			unset($old_targets[$key]);
			//echo "already in<br>";
		}else{
			//echo $i." ".$_POST['id'.$i]." ".$_POST['trans'.$i]." ".$key." DONT FIND!THAT HORRIBLE ERROR!WHYYYY??????!!!!!WHY YOU DO THAT???!!!<br>";
		}
	}
	$i++;
}

$targets=$new_targets;
if(count($targets)){	
	$main_query.=" target='".implode(",",$targets)."',";
}
$main_query.=" lang='".$_POST['lang']."'";

$main_query.=" WHERE id=".$_POST['id'];
mysql_query($main_query);
$err=mysql_error();

if($err){
	echo '{"error":"'.$err.'","query":"'.$main_query.'"}';
}else{
	//Отдаем обновленную строку таблицы (HTML)
	$query='SELECT t1.* FROM dt_W_'.$user['id'].' t1 WHERE id='.$_POST['id'];
	$line=mysql_fetch_assoc(mysql_query($query));
	$html="<td id='id".$_POST['tr_id']."'>
	".$line['id']."
	</td>
	<td>
	<a id='word".$_POST['tr_id']."' href='#tr".$line['id']."'>".$line['word']."</a>
	</td>
	<td>
		".$langs[$line['lang']]['name']."
		<input type='hidden' id='lang".$_POST['tr_id']."' value='".$line['lang']."'>
	</td>				
	<td id='targets".$_POST['tr_id']."'>";
	
	$targets=explode(",",$line['target']);
	foreach($targets as $target){
		$word=mysql_fetch_assoc(mysql_query("SELECT id,word,lang FROM  dt_W_".$user['id']." WHERE id=".$target));
		$html.="<a href='#tr".$target."'>".$word['word']."</a> (".$langs[$word['lang']]['ab'].") <a href='#".$_POST['tr_id']."' title='Удалить'>x</a><input type='hidden' class='targets".$_POST['tr_id']."' value='".$word['id']."' word='".$word['word']."' lang='".$word['lang']."'><br>";
	}
	$html.="	
	</td>
	<td>".$line['success']." из ".$line['attempts']." (".round(($line['success']/$line['attempts'])*100,0)."%, ".$line['status']." подряд)
	</td>
	<td>";
	if($langs[$line['lang']]['showlang']){
		if($line['status']<4){
			$html.="
		<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #f00;' checked name='show".$line['id']."' value='1'>/<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #0f0;' name='show".$line['id']."' value='0'>";
		}else{
			$html.="
		<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #f00;' name='show".$line['id']."' value='1'>/<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #0f0;' checked name='show".$line['id']."' value='0'>";
		}
	}else{
		$html.="Язык отключен";
	}
	$html.="
	</td>
	<td>
		<a href='delete_word.php?id=".$line['id']."'>Удалить</a>
		<a href='#".$_POST['tr_id']."' onclick='edit(".$_POST['tr_id'].");'>Изменить</a>
	</td>";
	
	echo json_encode(
		array(
			"html" => $html,
			"id"=>$line['id']
		)
	);
	if($debug){
		var_dump($old_word);
		var_dump($new_targets);
		var_dump($main_query);
		var_dump($err);
	}	
}
/*<input type="button" onclick="document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>'" autofocus value="Продолжить">
*/?>