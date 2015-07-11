<?if($_POST['data']){
include "top.php";
$strings=explode("\n",$_POST['data']);
//print_r($strings);
$postlang1=$_POST['lang1'];
$postlang2=$_POST['lang2'];
foreach($strings as $string){
	$temp=explode($_POST['selector'],$string);
	$postword1=mb_strtolower(trim($temp[0]),"UTF-8");
	$postword2=mb_strtolower(trim($temp[1]),"UTF-8");
	if($postword2){
		$query=mysql_query("SELECT * FROM dt_W_".$user['id']." WHERE (word LIKE '".$postword1."' AND lang=".$postlang1.") OR (word LIKE '".$postword2."' AND lang=".$postlang2.")");
		if($word1=mysql_fetch_assoc($query)){
			if($word2=mysql_fetch_assoc($query)){
				echo "Оба слова в базе<br>";
				print_r($word1);
				echo "<br>";
				print_r($word2);
				echo "<br>";
				
				$targets1=explode(",",$word1['target']);
				$targets2=explode(",",$word2['target']);
				print_r($targets1);
				echo "<br>";
				print_r($targets2);
				echo "<br>";
				if(!in_array($word2['id'],$targets1)){
					echo "в 1 нет 2<br>";
					if(strlen($word2['target'])){
						mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".$word2['id']."' WHERE id=".$word1['id']);
					}else{
						mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word2['id']."' WHERE id=".$word1['id']);
					}
					echo mysql_error();  
				}
				if(!in_array($word1['id'],$targets2)){
					echo "в 2 нет 1<br>";
					if(strlen($word2['target'])){
						mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word2['target'].",".$word1['id']."' WHERE id=".$word2['id']);
					}else{
						mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['id']."' WHERE id=".$word2['id']);
					}
					echo mysql_error();  
				}
			}else{
				echo "Одно словo в базе<br>";
				print_r($word1);
				if($word1['word']==$postword1){
					echo "В базе первое слово:".$word1['word']."=".$postword1."<br>";
					$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$postlang2."','0','','0','0','".$word1['id']."')";
					mysql_query($query);
					echo mysql_error();
					$id=mysql_insert_id();
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysql_insert_id()."' WHERE id=".$word1['id']);
					echo mysql_error();  
				}else{
					echo "В базе второе слово:".$word1['word']."=".$postword2."<br>";
					$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$postlang1."','0','','0','0','".$word1['id']."')";
					mysql_query($query);
					echo mysql_error();
					$id=mysql_insert_id();
					mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysql_insert_id()."' WHERE id=".$word1['id']);
					echo mysql_error(); 
				}
			}
		}else{
			echo "Ни одного слова в базе<br>";
			$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$postlang1."','0','','0','0','')";
			mysql_query($query);
			echo mysql_error();  

			$id=mysql_insert_id();
				
			$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$postlang2."','0','','0','0','".$id."')";
			mysql_query($query);
			echo mysql_error();  


			mysql_query("UPDATE dt_W_".$user['id']." SET target='".mysql_insert_id()."' WHERE id=".$id);
			echo mysql_error();  
		}		
	}
}
?>
<br>
<a id="link" href='../dict/'>На главную</a>
<?}else{
$page="many";
include "top.php";
?>
<table style='text-align:center;'>
<form action="add_many.php" method="post">
<tr><td>
<textarea name="data" style="width:400px;height:400px;">word1:слово1
word2:слово2</textarea><br>
<select name="lang1" onchange="document.cookie='lang1='+this.value+';';">
<?foreach($langs as $lang){
	echo "<option ";
	if($_COOKIE['lang1']==$lang['id']){echo "selected ";}
	echo "value='".$lang['id']."'>".$lang['name']."</option>";
}?>
</select>
<input type="text" name="selector" value=":" style="width:20px;height:20px;">
<select name="lang2" onchange="document.cookie='lang2='+this.value+';';">
<?foreach($langs as $lang){
	echo "<option ";
	if($_COOKIE['lang2']==$lang['id']){echo "selected ";}
	echo "value='".$lang['id']."'>".$lang['name']."</option>";
}?>
</select>
<br>
<input type="submit" value="Add All"><br>
</td></tr>
</form>
</table>
<?}?>