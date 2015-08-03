<?$page="add";
include "top.php";
if($debug) echo $_POST['word1']." ".$_POST['word2']."<br>";

$postword1=mb_strtolower(trim($_POST['word1']),"UTF-8");
$postword2=mb_strtolower(trim($_POST['word2']),"UTF-8");

if($debug) echo $postword1." ".$postword2;

if($postword1 and $postword2){
	$query=mysqli_query($mysqli,"SELECT * FROM dt_W_".$user['id']." WHERE (word LIKE '".$postword1."' AND lang='".$_POST['lang1']."') OR (word LIKE '".$postword2."' AND lang='".$_POST['lang2']."')");

	if($word1=mysqli_fetch_assoc($query)){
		if($word2=mysqli_fetch_assoc($query)){
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
					mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".$word2['id']."' WHERE id=".$word1['id']);
				}else{
					mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".$word2['id']."' WHERE id=".$word1['id']);
				}
				if($debug) echo mysqli_error($mysqli);  
			}
			if(!in_array($word1['id'],$targets2)){
				if($debug) echo "в 2 нет 1<br>";
				if(strlen($word2['target'])){
					mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".$word2['target'].",".$word1['id']."' WHERE id=".$word2['id']);
				}else{
					mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".$word1['id']."' WHERE id=".$word2['id']);
				}
				if($debug) echo mysqli_error($mysqli);  
			}
		}else{
			if($debug) echo "Одно словo в базе<br>";
			if($word1['word']==$postword1){
				//echo "В базе первое слово:".$word1['word']."=".$postword1."<br>";
				$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$_POST['lang2']."','0',NOW(),'0','0','".$word1['id']."')";
				mysqli_query($mysqli,$query);
				if($debug) echo mysqli_error($mysqli);
				$id=mysqli_insert_id($mysqli);
				mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysqli_insert_id($mysqli)."' WHERE id=".$word1['id']);
				echo mysqli_error($mysqli);  
			}else{
				//echo "В базе второе слово:".$word1['word']."=".$postword2."<br>";
				$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$_POST['lang1']."','0',NOW(),'0','0','".$word1['id']."')";
				mysqli_query($mysqli,$query);
				if($debug) echo mysqli_error($mysqli);
				$id=mysqli_insert_id($mysqli);
				mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysqli_insert_id($mysqli)."' WHERE id=".$word1['id']);
				if($debug) echo mysqli_error($mysqli); 
			}
		}
	}else{
		if($debug) echo "Ни одного слова в базе";
		$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$_POST['lang1']."','0',NOW(),'0','0','')";
		$query=mysqli_query($mysqli,$query);
		if($debug) echo mysqli_error($mysqli);  

		$id=mysqli_insert_id($mysqli);
			
		$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$_POST['lang2']."','0',NOW(),'0','0','".$id."')";
		mysqli_query($mysqli,$query);
		if($debug) echo mysqli_error($mysqli);  


		mysqli_query($mysqli,"UPDATE dt_W_".$user['id']." SET target='".mysqli_insert_id($mysqli)."' WHERE id=".$id);
		if($debug) echo mysqli_error($mysqli);  
	}
	//echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=".$_SERVER['HTTP_REFERER']."'>";
}else{
	if($debug) echo "Данные недополучены<br>";
	if($debug) print_r($_POST);
}
?>
<?if($debug) {?>
<br/>
<a id="link" href='<?echo $config['path'];?>'>На главную</a>
<?}else{?>
<script>
	//history.go(-1);
	document.location.href='<?if($_POST['back']){echo $_POST['back'];}else{echo $_SERVER['HTTP_REFERER'];}?>';
</script>
<?}?>