<?$page="stats";
$title="Словарь: статистика";
include "top.php";
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
	<div id="stats_container">
			<?
			$langs=mysqli_query($mysqli,"SELECT * FROM dt_lang_".$user['id']);
			while($lang=mysqli_fetch_assoc($langs)){
				echo "<hr/>".$lang['name'];
				if($lang['ab'])echo " (".$lang['ab'].")";
				if(!$lang['showlang']){
					echo " (отключен)";
//					continue;
				}
				echo ":<br/>";
			
				$query='SELECT t1.* FROM dt_W_'.$user['id'].' t1 ';
				$query.=" WHERE";
				$query.=" t1.lang=".$lang['id']." AND ";
				$s_query=$query;

				$query.=" (((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=1) AND t1.status<4)";
				$in_process=mysqli_num_rows(mysqli_query($mysqli,$query));

				/*				
				$query=$s_query." (((SELECT t2.showlang FROM dt_lang_".$user['id']." t2 WHERE t2.id=t1.lang)=0) OR t1.status>3)";
				$off=mysql_num_rows(mysql_query($query));
				echo "Неактивных:".$off."<br/>";
*/

				$query=$s_query." t1.status>3";
				$finish=mysqli_num_rows(mysqli_query($mysqli,$query));
				
				$query=$s_query." 1=1";
				$total=mysqli_num_rows(mysqli_query($mysqli,$query));
				
				$query=$s_query.'(( (t1.last_attempt <( NOW() - INTERVAL 1 DAY)) AND t1.status<4) OR  (t1.last_attempt < ( NOW() - INTERVAL (t1.status*15+1) DAY )))';
				$active=mysqli_num_rows(mysqli_query($mysqli,$query));

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
				
				
				echo "Всего ".$total." слов, изучено ".($total>0?$finish*100/$total:$total)."%, в процессе ".$in_process.", активных (включая напоминаемые) слов ".$active."<br/>";
				
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