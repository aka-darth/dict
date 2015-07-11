<?$page="check";
$title="Словарь: проверка";
include "top.php";
if($_GET['limit'] or $_GET['limit']=="0"){$limit=$_GET['limit'];}else{$limit="1";}
?>
<script>
	function save_parameter(){
		var limit=document.getElementById("limit").value;
		var lang=document.getElementById("lang").value;
		var to=document.getElementById("to").value;
		var eazy=document.getElementById("eazy").checked;
		var word=document.getElementById("which_word").value;
		var url='http://shcoding.esy.es/dict/test.php?limit='+limit+"&lang="+lang+"&to="+to+"&word="+word+"&eazy="+eazy;
		document.location.href=url;
	}
	
function fixEvent(e, _this) {
	e = e || window.event;
	if (!e.currentTarget) e.currentTarget = _this;
	if (!e.target) e.target = e.srcElement;
	if (!e.relatedTarget) {
		if (e.type == 'mouseover') e.relatedTarget = e.fromElement;
		if (e.type == 'mouseout') e.relatedTarget = e.toElement;
	}
	if (e.pageX == null && e.clientX != null ) {
		var html = document.documentElement;
		var body = document.body;
		e.pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
		e.pageX -= html.clientLeft || 0;
		e.pageY = e.clientY + (html.scrollTop || body && body.scrollTop || 0);
		e.pageY -= html.clientTop || 0;
	}
	if (!e.which && e.button) {
		e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : (e.button & 4 ? 2 : 0) );
	}
	return e;
}
	function activate_drag(e,elem){
<?if(!$_GET['lang'] and $_GET['lang']!=="0"){
// УБРАТЬ ЭТОТ ИНДУС-СТАЙЛ

?>
		e = fixEvent(e);
		var d_c=document.getElementById('drag_container');
		
		var from=document.getElementsByName('word'+elem.id.split("_")[1])[0];
		
		d_c.firstChild.nodeValue=elem.firstChild.nodeValue;
		d_c.style.top=elem.getBoundingClientRect().top;
		d_c.style.left=elem.getBoundingClientRect().left;
		d_c.style.color = 'black';
		d_c.style.display='block';
		
		var shiftX=elem.getBoundingClientRect().left-e.clientX;
		var shiftY=elem.getBoundingClientRect().top-e.clientY;
		
		console.log(shiftX+' '+shiftY);
		
		document.ondragstart=function(){return false;}
		document.onmousemove=function(e){
			e = fixEvent(e);
			d_c.style.top=e.pageY+shiftY;
			d_c.style.left=e.pageX+shiftX;
			return false;	
		}
		
		document.onmouseup=function(e){
			e = fixEvent(e);
			d_c.style.display='none';
			var where=document.elementFromPoint(e.clientX,e.clientY);
			if(where.tagName.toLowerCase()=='input'){
				from.value=document.getElementById('word_'+where.name.split('word')[1]+'_container').firstChild.nodeValue;
				where.value=d_c.firstChild.nodeValue;
			}
			d_c.firstChild.nodeValue=' ';
			document.onmousemove=null;
			document.onmouseup=null;
			return false;
		}
		return false;
<?}?>
	}
</script>
<div id="test_container">
<input type="hidden" id="which_word" value="<?if($_GET['word']){echo $_GET['word'];}?>">

<span id="drag_container" style='position:absolute;display:none;font-size:110%;' draggable="true"> </span>

	<table>
	<tr><td><br></td></tr>
	<tr>
	<td>eazy first <input id="eazy" type="checkbox" onchange="save_parameter(this)" <?if($_GET['eazy']=="true"){echo "checked";}?>></td>
		<td colspan="2">
			Переводить с:
			<select id="lang" onchange="save_parameter(this);">
				<option value="" <?if(!$_GET['lang'] and $_GET['lang']!=="0"){echo "selected";}?>>вперемешку</option>
				<?foreach($langs as $lang){
					echo "
				<option ";
					if($_GET['lang']==$lang['id']){echo "selected ";}
					echo "value='".$lang['id']."'>".$lang['name']."</option>";
				}?>
			</select>
		</td>
		<td colspan="2">
			Выводить по:
			<select id="limit" onchange="save_parameter(this);">
				<option value="1" <?if($limit=="1" or !$limit){echo "selected";}?>>1</option>
				<option value="2" <?if($limit=="2"){echo "selected";}?>>2</option>
				<option value="4" <?if($limit=="4"){echo "selected";}?>>4</option>
				<option value="8" <?if($limit=="8"){echo "selected";}?>>8</option>
				<option value="12" <?if($limit=="12"){echo "selected";}?>>12</option>
				<option value="16" <?if($limit=="16"){echo "selected";}?>>16</option>
				<option value="20" <?if($limit=="20"){echo "selected";}?>>20</option>
				<option value="24" <?if($limit=="24"){echo "selected";}?>>24</option>
				<option value="32" <?if($limit=="32"){echo "selected";}?>>32</option>
				<option value="40" <?if($limit=="40"){echo "selected";}?>>40</option>
				<option value="0" <?if($limit==="0"){echo "selected";}?>>Все</option>
			</select>			
		</td>
				<form action="check.php" method="post">
		<td colspan="3">
			Проверять перевод на 
			<select id="to" name="to" onchange="save_parameter(this);">
				<option value="" <?if(!$_GET['to'] and $_GET['to']!=="0"){echo "selected";}?>>любой</option>
				<?foreach($langs as $lang){
					echo "
				<option ";
					if($_GET['to']==$lang['id']){echo "selected ";}
					echo "value='".$lang['id']."'>".$lang['name']."</option>";
				}?>
			</select>
			язык
		</td>

	</tr>	
			<?
			include "../mysql.php";
			if($_GET['word']){//Возможность получить определённое слово по id
				$query='SELECT id,word FROM dt_W_'.$user['id'].' WHERE id='.$_GET['word'];				
			}else{
				$query='SELECT t1.id,t1.word,t1.status FROM dt_W_'.$user['id'].' t1 WHERE ';
				//		   (время последней попытки меньше чем сейчас минус час И статус<4) или (время последней попытки меньше чем сейчас минус месяц)
				$query.='(( (t1.last_attempt <( NOW() - INTERVAL 200 MINUTE )) AND t1.status<4) OR  (t1.last_attempt < ( NOW() - INTERVAL (t1.status*15) DAY )))';
				$query.=" AND ((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=1)";
				if($_GET['lang'] or $_GET['lang']==="0"){
					$query.=" AND lang=".$_GET['lang'];
				}
				if($_GET['eazy'] =="true"){
				  $query.=" ORDER BY status DESC";
				}else{
				   $query.=" ORDER BY RAND()";
				}
				if($limit){
					$query.=" LIMIT ".$limit;
				}else{
					//$query.=" ORDER BY word";
				}
			}
			$query=mysql_query($query);
			echo mysql_error();
			$i=0;
			echo "
		<tr>";
			if($line=mysql_fetch_assoc($query)){
				echo "
				<td>
					<input type='hidden' name='id".$i."' value='".$line['id']."'>
					";
				if($line['status']==3 or $line['status']=="3"){
					echo "<span class='blink'><span id='word_".$i."_container' onmousedown='activate_drag(event,this);'>".$line['word']."</span></span>";
				}else{
					echo "<span id='word_".$i."_container' onmousedown='activate_drag(event,this);'>".$line['word']."</span>";
				}
				echo "
				</td>
				<td>
					<input type='text' name='word".$i."' value='' autofocus>
					<span style='font-size:10px;'>(".$line['status'].")</span>
				</td>";
				$i++;
				$td=1;
				while($line=mysql_fetch_assoc($query)){
					echo "
				<td>
					<input type='hidden' name='id".$i."' value='".$line['id']."'>
					";
					if($line['status']==3 or $line['status']=="3"){
						echo "<span class='blink'><span id='word_".$i."_container' onmousedown='activate_drag(event,this);'>".$line['word']."</span></span>";
					}else{
						echo "<span id='word_".$i."_container' onmousedown='activate_drag(event,this);'>".$line['word']."</span>";
					}
					echo "
					</td>
				<td>
					<input type='text' name='word".$i."' value=''>
					<span style='font-size:10px;'>(".$line['status'].")</span>
				</td>";
					if(++$td>3){
						$td=0;
						echo "
			</tr>
			<tr>";
					}
					$i++;
				}
			}else{
				echo "
				<td colspan='4'>Ничего не найдено! Заходите позже.</td>";
			}
			echo "
		</tr>";
			?>
		<tr><td colspan="4" align="center"><input type="submit" value="Check"></td></tr>
		<tr>
			<td colspan="4">
				В режиме "вперемешку" слова можно перетаскивать мышкой на нужное поле ввода.
			</td>
		</tr>
		<tr>
			<td colspan="8">
				В этот раз правильно <?echo $_COOKIE['right'];?> из <?echo $_COOKIE['total'];?> (<?echo ($_COOKIE['right']*100/$_COOKIE['total']);?>%)
			</td>
		</tr>
		</form>
	</table>
</div>
</body>
</html>