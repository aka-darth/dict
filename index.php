<?$page="main";
$title="Добавить слово";
include "top.php";?>
<script>
function popup(html){
	var popup=document.getElementById('popup');
	popup.innerHTML='<input type="button" value="X" onclick="document.getElementById(\'fence\').style.display=\'none\';" style="float:right;width:30px;height:30px;border-radius:15px;font-weght:bold;"><br>'+html;
	document.getElementById('fence').style.display='block';

}
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
		if(document.getElementById('word1').value){
			var target=2;
		}else if(document.getElementById('word2').value){
			var target=1;
		}else{
			var popup=document.getElementById('popup');
			popup.innerHTML='<input type="button" value="X" onclick="document.getElementById(\'fence\').style.display=\'none\';" style="float:right;width:30px;height:30px;border-radius:15px;font-weght:bold;"><br>Не знаю,куда вставить';
			return false;
		}
		if(root.def[0].tr.length>1){
			var popup=document.getElementById('popup');
			popup.innerHTML='<input type="button" value="X" onclick="document.getElementById(\'fence\').style.display=\'none\';" style="float:right;width:30px;height:30px;border-radius:15px;font-weght:bold;">';
			for(var i=0;i<root.def[0].tr.length;i++){
				popup.innerHTML+="<input type='button' value='"+root.def[0].tr[i].text+"' onclick='document.getElementById(\"fence\").style.display=\"none\";document.getElementById(\"word"+target+"\").value=\""+
				root.def[0].tr[i].text.toLowerCase()+"\"'><br>";
				console.log(root.def[0].tr[i].text);
			}
			document.getElementById('fence').style.display="block";
		}else{
			document.getElementById('word'+target).value=root.def[0].tr[0].text.toLowerCase();
		}
		return false;
	}catch(e){
		console.log(e);
		console.log('Я не понял ответ от сервера:'+e.message);
		return false;
	}
}
function getYandex(word){
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
	if(!word){
		if(document.getElementById('word1').value){
			word=document.getElementById('word1').value;
			var lang=lang1+"-"+lang2;
		}else if(document.getElementById('word2').value){
			word=document.getElementById('word2').value;
			var lang=lang2+"-"+lang1;
		}else{
			popup('Введите слово!');
			return false;
		}
	}
	var script=document.createElement('script');
	script.type="text/javascript";
	//["ru-ru","ru-en","ru-pl","ru-uk","ru-de","ru-fr","ru-es","ru-it","ru-tr","en-ru","en-en","en-de","en-fr","en-es","en-it","en-tr","pl-ru","uk-ru","de-ru","de-en","fr-ru","fr-en","es-ru","es-en","it-ru","it-en","tr-ru","tr-en"]

	var src='https://dictionary.yandex.net/api/v1/dicservice.json/lookup?key=dict.1.1.20140407T210014Z.463834190667dfda.fc0cd649e650b8b577872032cbddfe0eb50f6da5&lang='+lang+'&text='+word+'&callback=callbackfunction';
	console.log(src);
	script.src=src;
	var head=document.getElementsByTagName('head')[0];
	head.appendChild(script);
	return false;
}
</script>
<div id="fence">
	<div id="popup">
	</div>
</div>
<div id="mainpage_container">
		<form action="add.php" method="post" onsubmit="">
		<table>
			<tr>
				<td>
					<select name="lang1" onchange="document.cookie='lang1='+this.value+';';">
					<?foreach($langs as $lang){
						echo "<option ";
						if($_COOKIE['lang1']==$lang['id']){echo "selected ";}
						echo "value='".$lang['id']."'>".$lang['name']."</option>";
					}?>
					</select>
				</td>
				<td>
					<select name="lang2" onchange="document.cookie='lang2='+this.value+';';">
					<?foreach($langs as $lang){
						echo "<option ";
						if($_COOKIE['lang2']==$lang['id']){echo "selected ";}
						echo "value='".$lang['id']."'>".$lang['name']."</option>";
					}?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<input type="text" id="word1" name="word1" value="" autofocus required tabindex="0" />
				</td>
				<td>
					<input type="text" id="word2" name="word2" value="" required tabindex="2" />
				</td>
			</tr> 
			<tr>
				<td colspan="2">
					<input type="submit" value="Add" tabindex="4" /><br>
		</form>
					<input type="button" id="yandex_button" onclick="getYandex();" tabindex="1" value="Перевести с помощью Яндекс.Словаря" />
				</td>
			</tr>
		</table>
</div>
	</body>
</html>