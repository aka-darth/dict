<?$page="stats";
$title="Словарь: статиска";
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
</script>
	<div id="stats_container">
			<?
			$langs=mysql_query("SELECT * FROM dt_lang_".$user['id']);
			while($lang=mysql_fetch_assoc($langs)){
				echo "<hr/>".$lang['name'];
				if($lang['ab'])echo " (".$lang['ab'].")";
				if(!$lang['showlang']){
					echo " (отключен)<br/>";
					continue;
				}
				echo ":<br/>";
			
				$query='SELECT t1.* FROM dt_W_'.$user['id'].' t1 ';
				$query.=" WHERE";
				$query.=" t1.lang=".$lang['id']." AND ";
				$s_query=$query;

				$query.=" (((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=1) AND t1.status<4)";
				$in_process=mysql_num_rows(mysql_query($query));

				/*				
				$query=$s_query." (((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=0) OR t1.status>3)";
				$off=mysql_num_rows(mysql_query($query));
				echo "Неактивных:".$off."<br/>";
*/

				$query=$s_query." t1.status>3";
				$finish=mysql_num_rows(mysql_query($query));
				
				$query=$s_query." 1=1";
				$total=mysql_num_rows(mysql_query($query));
				
				$query=$s_query.'(( (t1.last_attempt <( NOW() - INTERVAL 200 MINUTE )) AND t1.status<4) OR  (t1.last_attempt < ( NOW() - INTERVAL (t1.status*15) DAY )))';
				$active=mysql_num_rows(mysql_query($query));

				/*
				$last='SELECT MAX(last_attempt) FROM dt_W_'.$user['id'].' WHERE lang='.$lang['id'];
				$query=$s_query.' t1.last_attempt > (('.$last.') - INTERVAL 1 DAY) ';
				echo "Query:".$query."<br/>";
				$last_session=mysql_query($query);
				$off=mysql_num_rows($last_session);
				echo "КА:".$off."<br/>";
				while($word=mysql_fetch_assoc($last_session)){
					print_r($word);
				}
				*/
				
				
				echo "Всего ".$total." слов, изучено ".($finish*100/$total)."%, в процессе ".$in_process.", активных (включая напоминаемые) слов ".$active."<br/>";
				
				$err=mysql_error();
				if($err){
					echo $err."<br>".$s_query;
				}
				/* FILTER END */
				
				/* Print HTML */
				
				
			}
			?>
	</div>
</body>
</html>