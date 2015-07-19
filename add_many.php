<?
$page="many";
include "top.php";
if($_POST['data']){
	$strings=explode("\n",$_POST['data']);
	//print_r($strings);
	$postlang1=$_POST['lang1'];
	$postlang2=$_POST['lang2'];
	echo "<div id='add_many_results'>";
	foreach($strings as $string){
		$temp=explode($_POST['selector'],$string);
		$postword1=mb_strtolower(trim($temp[0]),"UTF-8");
		$postword2=mb_strtolower(trim($temp[1]),"UTF-8");
		if($postword2){
			$query=mysql_query("SELECT * FROM dt_W_".$user['id']." WHERE (word LIKE '".$postword1."' AND lang=".$postlang1.") OR (word LIKE '".$postword2."' AND lang=".$postlang2.")");
			if($word1=mysql_fetch_assoc($query)){
				if($word2=mysql_fetch_assoc($query)){
					if($debug){
						echo "Оба слова в базе<br>";
						print_r($word1);
						echo "<br>";
						print_r($word2);
						echo "<br>";
					}
					
					$targets1=explode(",",$word1['target']);
					$targets2=explode(",",$word2['target']);

					if($debug){
						print_r($targets1);
						echo "<br>";
						print_r($targets2);
						echo "<br>";
					}
					if(!in_array($word2['id'],$targets1)){
						if($debug)echo "в 1 нет 2<br>";
						if(strlen($word2['target'])){
							mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".$word2['id']."' WHERE id=".$word1['id']);
						}else{
							mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word2['id']."' WHERE id=".$word1['id']);
						}
						if($debug)echo mysql_error();  
					}
					if(!in_array($word1['id'],$targets2)){
						if($debug)echo "в 2 нет 1<br>";
						if(strlen($word2['target'])){
							mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word2['target'].",".$word1['id']."' WHERE id=".$word2['id']);
						}else{
							mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['id']."' WHERE id=".$word2['id']);
						}
						if($debug)echo mysql_error();  
					}
				}else{
					if($debug){
						echo "Одно словo в базе<br>";
						print_r($word1);
					}
					if($word1['word']==$postword1){
						if($debug)echo "В базе первое слово:".$word1['word']."=".$postword1."<br>";
						$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$postlang2."','0','','0','0','".$word1['id']."')";
						mysql_query($query);
						if($debug)echo mysql_error();
						$id=mysql_insert_id();
						mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysql_insert_id()."' WHERE id=".$word1['id']);
						if($debug)echo mysql_error();  
					}else{
						if($debug)echo "В базе второе слово:".$word1['word']."=".$postword2."<br>";
						$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$postlang1."','0','','0','0','".$word1['id']."')";
						mysql_query($query);
						if($debug)echo mysql_error();
						$id=mysql_insert_id();
						mysql_query("UPDATE dt_W_".$user['id']." SET target='".$word1['target'].",".mysql_insert_id()."' WHERE id=".$word1['id']);
						if($debug)echo mysql_error(); 
					}
				}
			}else{
				if($debug)echo "Ни одного слова в базе<br>";
				$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword1."','".$postlang1."','0','','0','0','')";
				mysql_query($query);
				if($debug)echo mysql_error();  

				$id=mysql_insert_id();
					
				$query="INSERT INTO dt_W_".$user['id']." VALUES ('','".$postword2."','".$postlang2."','0','','0','0','".$id."')";
				mysql_query($query);
				if($debug)echo mysql_error();  
				mysql_query("UPDATE dt_W_".$user['id']." SET target='".mysql_insert_id()."' WHERE id=".$id);
				if($debug)echo mysql_error();  
			}
		}
	}
	echo "</div>";
}
?>
<script>
var word,word_first;
function callbackfunction(root){
/*	for(key in root){
		console.log(key+' '+root[key]+' l:1');
		for(var key2 in root[key]){
			console.log(key2+' '+root[key][key2]+' l:2');
			for(var key3 in root[key][key2]){
				console.log(key3+' '+root[key][key2][key3]+' l:3');
				for(var key4 in root[key][key2][key3]){
					console.log(key3+' '+root[key][key2][key3][key4]+' l:4');
				}
			}
		}
	}
*/	
	try{
		console.log(word,word_first,root);
		for(var i=0;i<root.def[0].tr.length;i++){
			console.log(root.def[0].tr[i].text);
			var t=word_first?root.def[0].tr[i].text.toLowerCase()+":"+word:word+":"+root.def[0].tr[i].text.toLowerCase();
			document.getElementById('words').value+=t+'\n';
		}
		word=null;
		return false;
	}catch(e){
		document.getElementById('input_word').className="input_error";
		setTimeout(function(){this.className="";}.bind(document.getElementById('input_word')),3000);
			//Это варварство,я знаю.
		console.log(e);
		console.log('Я не понял ответ от сервера:'+e.message);
		return false;
	}
}
function getYandex(){
	if(word){
		console.log('not_ready..');
		return false;
	}
	switch(document.getElementsByName("lang1")[0].value){<?
		foreach($langs as $lang){
			echo "
		case '".$lang['id']."':lang1='".$lang['ab']."';break;";
		}?>
	}
	switch(document.getElementsByName("lang2")[0].value){<?
		foreach($langs as $lang){
			echo "
		case '".$lang['id']."':lang2='".$lang['ab']."';break;";
		}?>
	}
	if(document.getElementById('input_word').value){
		word=document.getElementById('input_word').value;
		lang=word.search(/[а-яё]/i)?"ru":"en";
		word_first=(lang==lang1)?true:false;
		lang=(word_first)?lang2+"-"+lang1:lang1+"-"+lang2;
		console.log(lang,lang1,lang2,word_first);
	}else{
		document.getElementById('input_word').className="input_error";
		setTimeout(function(){this.className="";}.bind(document.getElementById('input_word')),3000);
		return false;
	}
	var script=document.createElement('script');
	script.type="text/javascript";
	//["ru-ru","ru-en","ru-pl","ru-uk","ru-de","ru-fr","ru-es","ru-it","ru-tr","en-ru","en-en","en-de","en-fr","en-es","en-it","en-tr","pl-ru","uk-ru","de-ru","de-en","fr-ru","fr-en","es-ru","es-en","it-ru","it-en","tr-ru","tr-en"]

	script.src='https://dictionary.yandex.net/api/v1/dicservice.json/lookup?key=dict.1.1.20140407T210014Z.463834190667dfda.fc0cd649e650b8b577872032cbddfe0eb50f6da5&lang='+lang+'&text='+word+'&callback=callbackfunction';
	document.getElementsByTagName('head')[0].appendChild(script);
	return false;
}
</script>
<table style='text-align:center;'>
<form action="add_many.php" method="post">
<tr><td>
<textarea id="words" name="data" style="width:400px;height:400px;" placeholder="word1:слово1
word2:слово2"></textarea><br>
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
<br/>
<input type="text" id="input_word" value="" autofocus placeholder="Введите слово для перевода" onkeydown="if(event.keyCode==13){getYandex();return false;}"/>
<br/>
<input type="submit" value="Add All"><br>
</td></tr>
</form>
</table>