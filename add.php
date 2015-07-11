<?$page="add";
include "top.php";
if($debug) echo $_POST['word1']." ".$_POST['word2']."<br>";

$postword1=mb_strtolower(trim($_POST['word1']),"UTF-8");
$postword2=mb_strtolower(trim($_POST['word2']),"UTF-8");

if($debug) echo $postword1." ".$postword2;

if($postword1 and $postword2){
	$query=mysql_query("SELECT * FROM dt_W_".$user['id']." WHERE (word LIKE '".$postword1."' AND lang='".$_POST['lang1']."') OR (word LIKE '".$postword2."' AND lang='".$_POST['lang2']."')");

	if($word1=mysql_fetch_assoc($query)){
		if($word2=mysql_fetch_assoc($query)){
			if($debug) echo "Оба слова уже в базе<br>";
			//print_r($word1);
			//echo "<br>";
			//print_r($word2);
			//echo "<br>";
			
			$targets1=explode(",",$word1['target']);
			$targets2=explode(",",$word2['target']);
			//print_r($targets1);
			//echo "<br>";
			//print_r($targets2);
			//echo "<br>";
			if(!in_array($word2['id'],$targets1)){
				if($debug) echo "в 1 нет 2<br>";
				if(strlen($word2['target'])){
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".$word2['id']."' WHERE id=".$word1['id']);
				}else{
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word2['id']."' WHERE id=".$word1['id']);
				}
				if($debug) echo mysql_error();  
			}
			if(!in_array($word1['id'],$targets2)){
				if($debug) echo "в 2 нет 1<br>";
				if(strlen($word2['target'])){
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word2['target'].",".$word1['id']."' WHERE id=".$word2['id']);
				}else{
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['id']."' WHERE id=".$word2['id']);
				}
				if($debug) echo mysql_error();  
			}
		}else{
			if($debug) echo "Одно словo в базе<br>";
			if($word1['word']==$postword1){
				//echo "В базе первое слово:".$word1['word']."=".$postword1."<br>";
				$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$_POST['lang2']."','0','','0','0','".$word1['id']."')";
				mysql_query($query);
				if($debug) echo mysql_error();
				$id=mysql_insert_id();
				mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysql_insert_id()."' WHERE id=".$word1['id']);
				echo mysql_error();  
			}else{
				//echo "В базе второе слово:".$word1['word']."=".$postword2."<br>";
				$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$_POST['lang1']."','0','','0','0','".$word1['id']."')";
				mysql_query($query);
				if($debug) echo mysql_error();
				$id=mysql_insert_id();
				mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysql_insert_id()."' WHERE id=".$word1['id']);
				if($debug) echo mysql_error(); 
			}
		}
	}else{
		if($debug) echo "Ни одного слова в базе";
		$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$_POST['lang1']."','0','','0','0','')";
		mysql_query($query);
		if($debug) echo mysql_error();  

		$id=mysql_insert_id();
			
		$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$_POST['lang2']."','0','','0','0','".$id."')";
		mysql_query($query);
		if($debug) echo mysql_error();  


		mysql_query("UPDATE dt_W_".$user['id']." SET target='".mysql_insert_id()."' WHERE id=".$id);
		if($debug) echo mysql_error();  
	}
	//echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=".$_SERVER['HTTP_REFERER']."'>";
}else{
	if($debug) echo "Данные недополучены<br>";
	if($debug) print_r($_POST);
}
?>
<?if($debug) {?>
<br/>
<a id="link" href='../dict/'>На главную</a>
<?}else{?>
<script>
	//history.go(-1);
	document.location.href='http://shcoding.esy.es/dict/';
</script>
<?}?>