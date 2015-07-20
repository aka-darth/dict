<?
$page="check";
$title="Словарь: проверка";
include "top.php";
if($_GET['limit'] or $_GET['limit']=="0"){$limit=$_GET['limit'];}else{$limit=1;}
?>
<script>
	function save_parameter(){
		var limit=document.getElementById("limit").value;
		var lang=document.getElementById("lang").value;
		var to=document.getElementById("to").value;
		var eazy=document.getElementById("eazy").checked;
		var word=document.getElementById("which_word").value;
		var url='<?echo $config['path'];?>/test.php?limit='+limit+"&lang="+lang+"&to="+to+"&word="+word+"&eazy="+eazy;
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
	function activate_drag(e,elem,touch){
<?if(!$_GET['lang'] and $_GET['lang']!=="0"){
// УБРАТЬ ЭТОТ ИНДУС-СТАЙЛ

?>		
		if(touch){
			var e=e.targetTouches[0];
			var lastx=e.clientX;
			var lasty=e.clientY;
		}else{
			e = fixEvent(e);
		}
		var d_c=document.getElementById('drag_container');
		var from=document.getElementsByName('word'+elem.id.split("_")[1])[0];
		var shiftX=elem.getBoundingClientRect().left-e.clientX;
		var shiftY=elem.getBoundingClientRect().top-e.clientY;
		
		d_c.firstChild.nodeValue=elem.firstChild.nodeValue;
		d_c.style.top=e.clientY;
		d_c.style.left=e.clientX;
		d_c.style.color = 'black';
		d_c.style.position="absolute";
		d_c.style.display='block';
//			alert(shiftX+' '+shiftY);
		document.ondragstart=function(){return false;}
		if(touch){
			document.addEventListener('touchmove',function(event){
				if (event.targetTouches.length == 1) {
					var touch = event.targetTouches[0];
					d_c.style.left=(touch.pageX+shiftX)+'px';
					d_c.style.top=(touch.pageY +shiftY)+'px';
					lastx=touch.pageX;
					lasty=touch.pageY;
				}
//				event.preventDefault();
				return false;
			});
		}else{
			document.onmousemove=function(e){
				e = fixEvent(e);
				d_c.style.top=e.pageY+shiftY;
				d_c.style.left=e.pageX+shiftX;
				return false;	
			}	
		}
		
		document.ontouchend=document.onmouseup=function(e){
			d_c.style.display='none';
			if(touch){
				var where=document.elementFromPoint(lastx,lasty);
			}else{
				e = fixEvent(e);
				var where=document.elementFromPoint(e.clientX,e.clientY);
			}
			if(where.tagName.toLowerCase()=='input'){
				from.value=document.getElementById('word_'+where.name.split('word')[1]+'_container').firstChild.nodeValue;
				where.value=d_c.firstChild.nodeValue;
			}else{
				
			}
			d_c.firstChild.nodeValue=' ';
			document.onmousemove=null;
			document.ontouchend=document.onmouseup=null;
			return false;
		}
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
			if($_GET['word']){//Возможность получить определённое слово по id
				$query='SELECT id,word FROM dt_W_'.$user['id'].' WHERE id='.$_GET['word'];				
			}else{
				if(($_GET['lang'] or $_GET['lang']==="0") or (!$limit or $limit<16)){
					$what='SELECT t1.id,t1.word,t1.status';
					$what.=',t1.last_attempt,t1.lang ';
					$from='FROM dt_W_'.$user['id'].' t1 JOIN dt_lang_'.$user['id'].' lang ON t1.lang=lang.id WHERE ';
					//		   (время последней попытки меньше чем сейчас минус час И    статус<4) или(время последней попытки меньше чем сейчас минус )
					$where='((t1.last_attempt < (NOW() - INTERVAL 200 MINUTE) AND t1.status<4) OR (t1.last_attempt < (NOW() - INTERVAL (t1.status*15+1) DAY)))';
					$where.=" AND lang.showlang=1";
					if($_GET['lang'] or $_GET['lang']==="0"){
						$where.=" AND lang=".$_GET['lang'];
					}
					if($_GET['eazy'] =="true"){
						$order.=" ORDER BY status DESC";
					}else{
						$order.=" ORDER BY RAND()";
					}
					$query=$what.$from.$where.$order;
				}else if($limit > 15){//Вперемешку
					$double_array=true;
				
					$query="SELECT DISTINCT t1.id,t1.word,t1.status,t2.id AS id2,t2.word AS word2,t2.status AS status2";
					$query.=',t2.last_attempt AS last2,t2.lang AS lang2,t1.last_attempt,t1.lang ';
					$query.=' FROM (dt_W_'.$user['id'].' AS t1 JOIN dt_lang_'.$user['id'].' AS lang1 ON t1.lang=lang1.id) JOIN  
					(dt_W_'.$user['id'].' AS t2 JOIN dt_lang_'.$user['id'].' lang2 ON t2.lang=lang2.id) ON (
					t1.id < t2.id AND
					FIND_IN_SET(t2.id,t1.target) AND 
					FIND_IN_SET(t1.id,t2.target)
					)
					WHERE
					lang1.showlang=1 AND
					lang2.showlang=1 AND
					((t1.last_attempt < (NOW() - INTERVAL 200 MINUTE) AND t1.status<4) OR (t1.last_attempt < (NOW() - INTERVAL 215 DAY)))
					AND
					((t2.last_attempt < (NOW() - INTERVAL 200 MINUTE) AND t2.status<4) OR (t2.last_attempt < (NOW() - INTERVAL 215 DAY)))
					';
					if($_GET['eazy'] =="true"){
						$query.=" ORDER BY t1.status+t2.status DESC";
					}else{
						$query.=" ORDER BY RAND()";
					}
					$limit=$limit/2;
				}
				if($limit){
					$query.=" LIMIT ".$limit;
				}
			}
		if($debug){?>
		<tr>
			<td colspan="8" style=''>
			<code>
				<?
				echo $query."<hr/>";
				echo "num ".mysqli_num_rows(mysqli_query($mysqli,$query))."<hr/>";
				echo "<hr/>";
				?>
			</code>
			</td>
		</tr>
		<?}
			$words_array=array();
			$query=mysqli_query($mysqli,$query);
			while($line=mysqli_fetch_array($query)){
				if($double_array){
					$words_array[]=array('id'=>$line['id'],'status'=>$line['status'],'word'=>$line['word'],'lang'=>$line['lang'],'last_attempt'=>$line['last_attempt']);
					$words_array[]=array('id'=>$line['id2'],'status'=>$line['status2'],'word'=>$line['word2'],'lang'=>$line['lang2'],'last_attempt'=>$line['last2']);
				}else{
					$words_array[]=$line;
				}
			}
			shuffle($words_array);
			
			if($double_array){
				$langs=array();
				foreach($words_array as $id => $word){
					if($langs[$word['lang']]){
						$langs[$word['lang']]++;
						if($langs[$word['lang']]>$max){
							$max=$langs[$word['lang']];
							$max_key=$word['lang'];
						}
					}else{
						$langs[$word['lang']]=1;
						if(!isset($max)){
							$max = 1;
							$max_key=$word['lang'];
						}
					}
				}
				
				$t=1;
				for($i=0;$i<count($words_array);$i++){
					if($t and $words_array[$i]['lang']!=$max_key){
						$alt_t=1;
						for($j=$i;$j<count($words_array);$j++){
							if(!$alt_t and $words_array[$j]['lang']==$max_key){
								list($words_array[$i],$words_array[$j])=array($words_array[$j],$words_array[$i]);
								break;
							}
							$alt_t=($alt_t)?(0):(1);
						}
					}else if(!$t and $words_array[$i]['lang']==$max_key){
						$alt_t=$t;
						for($j=$i;$j<count($words_array);$j++){
							if($alt_t and $words_array[$j]['lang']!=$max_key){
								list($words_array[$i],$words_array[$j])=array($words_array[$j],$words_array[$i]);
								break;
							}
							$alt_t=($alt_t)?(0):(1);
						}
					}
					$t=($t)?(0):(1);
				}
			}
			
			
			
			
			
		if($debug){?>
		<tr>
			<td colspan="8" style=''>
			<code>
				<?
				echo "<pre>";
				if(!$limit) echo "Total:".count($words_array);
				print_r($words_array);
				echo "</pre>";
				?>
			</code>
			</td>
		</tr>
		<?}
			echo "
		<tr>";
			if($words_array[0]){//Первое слово
				echo "
				<td>
					<input type='hidden' name='id0' value='".$words_array[0]['id']."'>
					<span";
				if(intval($words_array[0]['status'])>2){echo " class='blink'";}
				echo " id='word_0_container' ontouchstart='activate_drag(event,this,true);' onmousedown='activate_drag(event,this);'>".$words_array[0]['word']."</span>";
				if($debug)echo " <a href='".$config['path']."/allwords.php?opened=1&word=".$words_array[0]['id']."' target='_blank' style='font-size:10px;'>Edit</a>";
				echo "
				</td>
				<td>
					<input type='text' name='word0' value='' autofocus>";
				if($debug)echo "<br/><span style='font-size:10px;'>(Id=".$words_array[0]['id'].",S=".$words_array[0]['status'].",L=".$words_array[0]['last_attempt'].")</span>";
				echo "
				</td>";
				$td=1;
				for($i=1;$i<count($words_array);$i++){//Остальные
					echo "
				<td>
					<input type='hidden' name='id".$i."' value='".$words_array[$i]['id']."'>
					<span";
					if(intval($words_array[$i]['status'])>2){echo " class='blink'";}
					echo " id='word_".$i."_container' ontouchstart='activate_drag(event,this,true);' onmousedown='activate_drag(event,this);'>".$words_array[$i]['word']."</span>";
					if($debug)echo " <a href='".$config['path']."/allwords.php?opened=1&word=".$words_array[$i]['id']."' target='_blank' style='font-size:10px;'>Edit</a>";
					echo "
					</td>
				<td>
					<input type='text' name='word".$i."' value=''>";
					if($debug)echo "<br/><span style='font-size:10px;'>(Id=".$words_array[$i]['id'].",S=".$words_array[$i]['status'].",L=".$words_array[$i]['last_attempt'].")</span>";
					echo "
				</td>";
					if($double_array){
						if(++$td>1){
							$td=0;
							echo "
				</tr>
				<tr>";
						}
					}else{
						if(++$td>3){
							$td=0;
							echo "
				</tr>
				<tr>";
						}
					}
				}
			}else{
				echo "
				<td colspan='4'>Нет слов для проверки! Заходите позже.</td>";
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
		<?if($_COOKIE['total']){?>
		<tr>
			<td colspan="8">
				В этот раз правильно <?echo $_COOKIE['right'];?> из <?echo $_COOKIE['total'];?> (<?echo ($_COOKIE['right']*100/$_COOKIE['total']);?>%)
			</td>
		</tr>
		<?}?>
		</form>
	</table>
</div>
</body>
</html>