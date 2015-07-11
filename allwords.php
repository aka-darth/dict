<?$page="all";
$title="Словарь: слова";
include "top.php";
include "../mysql.php";
$filter=array();
//Тут надо перевести всё это дело на куки
if($_GET['opened']){
	$filter['opened']=$_GET['opened'];
	if($_GET['word']){
		$filter['word']=$_GET['word'];
	}else{
		$filter['word']=null;
	}
	if($_GET['lang']){
		$filter['lang']=$_GET['lang'];
	}else{
		$filter['lang']=null;
	}
	if($_GET['search']){
		$filter['search']=$_GET['search'];
	}else{
		$filter['search']=null;
	}
}else{
	$filter['opened']=false;
}
if($_GET['active']){
	$filter['active']=$_GET['active'];
}else{
	$filter['active']="both";
}
?>
<style>
	#edit_form_div{
		position:fixed;
		left:50%;
		top:40%;
		margin-left:-222px;
		width:444px;
		display:none;
		background:#777;
		border:3px solid #333;
		z-index:200;
	}
	#edit_form input[type=text]{
		width:160px;
	}
	#fence{
		position:fixed;
		width:100%;
		height:100%;
		z-index:100;
		background:rgba(170,99,0,0.6);
		display:none;
		padding:0;
		margin:0;
	}
	td{
		border-bottom:1px solid black;
		border-left:1px solid black;
		background:rgba(11,11,11,0.0);
		-webkit-transition: all .5s linear;
	}
	td a{
		color:innerhit;
	}
	.hat .active{
		border:1px solid white;
		border-top:none;
		background:rgba(255,255,255,0.3);
	}
	.hat td{
		cursor:pointer;
	}
	.hat td:hover{
		background:rgba(255,125,0,0.7);
		border:1px solid #f90;
	}
	tr:target td{
		//border:2px solid #999;
		background:rgba(11,11,11,0.6);
		top:10%;
	}
</style>
<script>
	function set_activity(id,that){
		xhr_send('activity.php?id='+id+'&enabled='+that.value,function(response){
			if(response.trim()=='ok'){
				if(that.value){
					that.parentNode.parentNode.className='inactive_word';
				}else{
					that.parentNode.parentNode.className='';
				}
			}else{
				alert(response);
			}
		});
		
	}
	function sort(data){
		document.getElementById("sort").value=data;
		save_parameter();
	}
	function show_filter(){
		var filter=document.getElementsByClassName('filter');
		for(var i=0;i<filter.length;i++){
			filter[i].style.display=(filter[i].style.display=='none')?('table-row'):('none');
		}
		var filter=document.getElementsByClassName('hat');
		for(var i=0;i<filter.length;i++){
			filter[i].style.display=(filter[i].style.display=='none')?('table-row'):('none');
		}
	}
	function save_parameter(){
		var word=document.getElementById("which_word").value;
		var lang=document.getElementById("lang").value;
		var sort=document.getElementById("sort").value;
		var search=document.getElementById("search").value;
		var opened=(document.getElementsByClassName('filter')[0].style.display=="none")?(""):("1");
		if(document.getElementById("show_active").checked){
			if(document.getElementById("show_learned").checked){
				var active="both";
			}else{
				var active="on";
			}
		}else{
			if(document.getElementById("show_learned").checked){
				var active="off";
			}else{
				document.getElementById("show_learned").checked=true;
				document.getElementById("show_active").checked=true;
				var active="both";
			}
		}
		var url='http://shcoding.esy.es/dict/allwords.php?sort='+sort+'&opened='+opened+'&word='+word+'&lang='+lang+"&search="+search+"&active="+active;
		document.location.href=url;
	}
	function edit(num){
		document.getElementById('fence').style.display='block';
		document.getElementById('edit_form_div').style.display='block';
		
		var input_word=document.getElementsByName("word")[0];
		var input_lang=document.getElementsByName("lang")[0];
		var input_id=document.getElementsByName("id")[0];
		var tr_id=document.getElementsByName("tr_id")[0];
		
		var id=document.getElementById('id'+num).firstChild.nodeValue;
		var word=document.getElementById('word'+num).firstChild.nodeValue;
		
		tr_id.value=num;
		input_id.value=id;
		input_word.value=word.trim();
		input_lang.value=document.getElementById('lang'+num).value;

		fields=0;
		var targets=document.getElementsByClassName('targets'+num);
		for(var i=0;i<targets.length;i++){
			add_field(targets[i].getAttribute("word"),targets[i].getAttribute("lang"),targets[i].value);
		}
	}
	function try_edit(){
		var url="http://shcoding.esy.es/dict/edit_word.php";
		var xhr = new XMLHttpRequest();
		var form=document.getElementById('edit_form');
		var data='from=web';
		for(var i=0;i<form.elements.length;i++){
			if(form.elements[i].name){
				data+='&'+form.elements[i].name+'='+form.elements[i].value;
			}
		}
		xhr.onreadystatechange=function(id){
			if(xhr.readyState==4){
				if(xhr.status==200){
					try{
						console.log(xhr.responseText);
						if(xhr.responseText.indexOf('Error')<0){
							document.getElementById('fence').style.display='none';
							document.getElementById('edit_form_div').style.display='none';
							document.getElementById('fields').innerHTML='';
							id=parseInt(xhr.responseText.split("'>")[1].split("</td>")[0]);//костыль(
							console.log(id);
							console.log(document.getElementById('tr'+id));
							document.getElementById('tr'+id).innerHTML=xhr.responseText;
						}else{
							console.log(error);
						}
					}catch(e){
						console.log('Error in xhr callback:'+e.stack);
					}
				}else{
					console.log('XHR status='+xhr.status+'; url='+url+';');
				}
			}
		}
		try{
			xhr.open('POST', url, true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.send(data);
		}catch(e){
			console.log('Send XHR error:'+e.stack);
		}
	}
	function add_field(word,lang,id){
		var div=document.createElement('div');
		div.className='field';
		div.id='field'+fields;
		var html='<input type="text" name="trans'+fields+'" ';
		html+=(word)?("value='"+word+"' "):("placeholder='Вариант перевода' ");
		html+=' required>';
		html+='<input type="hidden" name="id'+fields+'" value="'+((id)?(id):('new'))+'">';
		html+='<select name="lang'+fields+'" required>';
		html+='<option disabled>Выберите язык</option>';
		<?foreach($langs as $lang){
			echo "
		html+='<option value=\"".$lang['id']."\" ';
		html+=(lang==".$lang['id'].")?('selected '):('');
		html+='>".$lang['name']."</option>';";
		}?>		
		html+='</select>';
		html+='<input type="button" onclick="delete_field(this);" value="Delete">';
		div.innerHTML=html;
		document.getElementById('fields').appendChild(div);
		fields++;
	}
	function delete_field(that){
		var id=that.parentNode.id;
		//console.log(id);
		that.parentNode.parentNode.removeChild(that.parentNode);
		fields--;
		id=1*id.split('field')[1];
//		console.log('field'+(+id+1)+' '+document.getElementById('field'+(+id+1)));
		
		for(var i=id+1;f=document.getElementById('field'+(i*1));i++){
			try{
			f.id='field'+(i-1);
			f.childNodes[0].name='trans'+(i-1);
			f.childNodes[1].name='id'+(i-1);
			f.childNodes[2].name='lang'+(i-1);
			//console.log(i+' to '+(i-1));
			}catch(e){console.log(e.message);}
		}
	}
</script>
<div id="fence" onclick="this.style.display='none';document.getElementById('edit_form_div').style.display='none';document.getElementById('fields').innerHTML='';">
</div>

	<form action="edit_word.php" method="post" id="edit_form" onsubmit="try_edit();return false;">
<div id="edit_form_div">
		<div class="field">
			<input type="text" name="word" value="" required><select name="lang" required>
			<?foreach($langs as $lang){
				echo "
				<option value='".$lang['id']."'>".$lang['name']."</option>";
			}?>
			</select>
		<div id='fields'>
		
		</div>
			<input type="button" value="add field" onclick="add_field();">
			<input type="submit" value="Save">
			<input type="hidden" name="id" value="">
			<input type="hidden" name="tr_id" value="">
		</div>
</div>
	</form>
	
	
<input type='hidden' id='sort' value='<?echo $_GET['sort'];?>'>
	<table>
		<tr class='hat' <?if($filter['opened']){echo "style='display:none;'";}?>>
			<td onclick="sort('<?
			if($_GET['sort']=="n_up"){
				echo 'n_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="n_down"){
				echo 'n_up\');" class="active"> &#8595; ';
			}else{
				echo 'n_down\');" >';
			}
			?>№</td>
			<td onclick="sort('<?
			if($_GET['sort']=="w_up"){
				echo 'w_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="w_down"){
				echo 'w_up\');" class="active"> &#8595; ';
			}else{
				echo 'w_down\');">';
			}
			?>Слово </td>
			<td onclick="sort('<?
			if($_GET['sort']=="l_up"){
				echo 'l_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="l_down"){
				echo 'l_up\');" class="active"> &#8595; ';
			}else{
				echo 'l_down\');">';
			}
			?>Язык</td>
			<td>Переводы</td>
			<td onclick="sort('<?
			if($_GET['sort']=="s_up"){
				echo 's_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="s_down"){
				echo 's_up\');" class="active"> &#8595; ';
			}else{
				echo 's_down\');">';
			}
			?>Статистика</td>
			<td  onclick="sort('<?
			if($_GET['sort']=="c_up"){
				echo 'c_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="c_down"){
				echo 'c_up\');" class="active"> &#8595; ';
			}else{
				echo 'c_down\');">';
			}
			?>Изучивается<br>Да / Нет</td>
			<td onclick="show_filter();">Показать фильтр</td>
		</tr>
		<tr class='filter' <?if(!$filter['opened']){echo "style='display:none;'";}?>>
			<td class='button' onclick="sort('<?
			if($_GET['sort']=="n_up"){
				echo 'n_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="n_down"){
				echo 'n_up\');" class="active"> &#8595; ';
			}else{
				echo 'n_down\');" >';
			}
			?>№</td>
			<td class='button' onclick="sort('<?
			if($_GET['sort']=="w_up"){
				echo 'w_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="w_down"){
				echo 'w_up\');" class="active"> &#8595; ';
			}else{
				echo 'w_down\');">';
			}
			?>Поиск</td>
			<td class='button' onclick="sort('<?
			if($_GET['sort']=="l_up"){
				echo 'l_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="l_down"){
				echo 'l_up\');" class="active"> &#8595; ';
			}else{
				echo 'l_down\');">';
			}
			?>Язык</td>
			<td>Переводы</td>
			<td class='button' onclick="sort('<?
			if($_GET['sort']=="s_up"){
				echo 's_down\');" class="active"> &#8593; ';
			}else if($_GET['sort']=="s_down"){
				echo 's_up\');" class="active"> &#8595; ';
			}else{
				echo 's_down\');">';
			}
			?>Статистика</td>
			<td>Изучивается</td>
			<td class='button' onclick="document.location.href='./allwords.php?opened=1';">Очистить фильтр</td>
		</tr>
		<tr class='filter' <?if(!$filter['opened']){echo "style='display:none;'";}?>>
			<td><input type='text' size='5' id='which_word' onchange='save_parameter();' value="<?echo $filter['word'];?>"></td>
			<td><input type='text' id='search' onchange='save_parameter();' value="<?echo $filter['search'];?>" style="width:100%;"></td>
			<td>
				<select id="lang" onchange='save_parameter();'>
					<option value="">Все языки</option>
				<?foreach($langs as $lang){
					echo "
					<option ";
					if($_GET['lang']==$lang['id']){echo "selected ";}
					echo "value='".$lang['id']."'>".$lang['name']."</option>";
				}?>
				</select>
			</td>
			<td>колво от до переводы на - чекбоксы</td>
			<td></td>
			<td>
Да<input id='show_active' type='checkbox' onchange='save_parameter();' <?if($filter['active']=='on' or $filter['active']=='both'){echo " checked";}?> style='border:1px solid #f00;'>
/ Нет<input id='show_learned' type='checkbox' onchange='save_parameter();' <?if($filter['active']=='off' or $filter['active']=='both'){echo " checked";}?> style='border:1px solid #f00;'>
			</td>
			<td class='button' onclick="show_filter();">Скрыть фильтр</td>
		</tr>
			<?//Конструирование запроса в бд с учетом параметров
			$query='SELECT t1.* FROM dt_W_'.$user['id'].' t1 ';
			/* FILTER */
			if($filter['opened']){
				$query.=" WHERE";
				if($filter['word']){
					$query.=" t1.id=".$filter['word']." AND ";
				}
				if($filter['search']){
					$query.=" t1.word LIKE '".$filter['search']."%' AND";
				}
				if($filter['lang']){
					$query.=" t1.lang=".$filter['lang']." AND ";
				}
				switch($filter['active']){
					case "on":
						$query.=" (((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=1) AND t1.status<4)";
					break;
					case "off":
						$query.=" (((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=0) OR t1.status>3)";
					break;
					default:case "both":$query.=" 1=1 ";break;
				}
			}
			switch($_GET['sort']){
				case "c_up":$query.=" ORDER BY (SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang),t1.status DESC";break;
				case "c_down":$query.=" ORDER BY (NOT (SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)),t1.status";break;
				case "s_up":$query.=" ORDER BY t1.status DESC";break;
				case "s_down":$query.=" ORDER BY t1.status";break;
				case "n_up":$query.=" ORDER BY t1.id DESC";break;
				case "n_down":$query.=" ORDER BY t1.id";break;
				case "l_up":$query.=" ORDER BY t1.lang DESC";break;
				case "l_down":$query.=" ORDER BY t1.lang";break;
				case "w_up":$query.=" ORDER BY t1.word DESC";break;
				case "w_down":default:$query.=" ORDER BY t1.word";
			}
			$s_query=$query;
			$query=mysql_query($query);
			$err=mysql_error();
			if($err){
				echo $err."<br>".$s_query;
			}
			/* FILTER END */
			
			/* Print HTML */
			$i=0;
			while($line=mysql_fetch_assoc($query)){
				if($langs[$line['lang']]['showlang']==1 and $line['status']<4){
					echo "<tr id='tr".$line['id']."' class='active_word'>";
				}else{
					echo "<tr id='tr".$line['id']."' class='inactive_word'>";
				}
				echo "
				<td id='id".$i."'>
				".$line['id']."
				</td>
				<td>
				<a id='word".$i."' href='#tr".$line['id']."'>".$line['word']."</a>
				</td>
				<td>
					".$langs[$line['lang']]['name']."
					<input type='hidden' id='lang".$i."' value='".$line['lang']."'>";
				echo "
				</td>				
				<td id='targets".$i."'>";
				$targets=explode(",",$line['target']);
				foreach($targets as $target){
					$word=mysql_fetch_assoc(mysql_query("SELECT id,word,lang FROM  dt_W_".$user['id']." WHERE id=".$target));
					echo "<a href='#tr".$target."'>".$word['word']."</a> (".$langs[$word['lang']]['ab'].") <a href='#".$i."' title='Удалить'>x</a><input type='hidden' class='targets".$i."' value='".$word['id']."' word='".$word['word']."' lang='".$word['lang']."'><br>";
				}
				echo "	
				</td>
				<td>";
					echo $line['success']." из ".$line['attempts']." (".round(($line['success']/$line['attempts'])*100,0)."%, ".$line['status']." подряд)";
				echo "
				</td>
				<td>";
				if($langs[$line['lang']]['showlang']){
					if($line['status']<4){
						echo "
					<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #f00;' checked name='show".$line['id']."' value='1'>/<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #0f0;' name='show".$line['id']."' value='0'>";
					}else{
						echo "
					<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #f00;' name='show".$line['id']."' value='1'>/<input type='radio' onclick='set_activity(".$line['id'].",this);' style='border:1px solid #0f0;' checked name='show".$line['id']."' value='0'>";
					}
				}else{
					echo "Язык отключен";
				}
				echo "
				</td>
				<td>
					<a href='delete_word.php?id=".$line['id']."'>Удалить</a>
					<a href='#".$i."' onclick='edit(".$i.");'>Изменить</a>
				</td>
				</tr>";
				$i++;
			}
			if($i==0){
				echo "<tr><td colspan='7'>Ничего не найдено!<br>";
				print_r($filter);
				echo "</td></tr>";
			}
			?>
	</table>
</body>
</html>