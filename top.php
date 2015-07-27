<?include "config.php";
include "login.php";
if($user){
	$query=mysqli_query($mysqli,"SELECT * FROM dt_lang_".$user['id']);
	echo mysql_error();
	$langs=Array();
	while($lang=mysqli_fetch_assoc($query)){
		if($lang['showlang'] or $page=="all"){
			$langs[$lang['id']]=array();
			$langs[$lang['id']]=$lang;
		}
	}
	if(count($langs)==0 and $page!="langs"){
		?>
	<script>
		document.location.href='<?echo $config['path'];?>/langs.php';
	</script>
		<?
	}else if(!mysqli_num_rows(mysqli_query($mysqli,"SELECT * FROM dt_W_".$user['id'])) and $_SERVER['SCRIPT_NAME']!='/index.php'){
		?>
	<script>
		document.location.href='<?echo $config['path'];?>/';
	</script>
		<?		
	}
}
?>
	<script>
		document.innerHTML='';//Экзорцизм
	</script>
<html>
	<head>
		<meta name="google" value="notranslate"><!--заебал внатуре-->
		<title><?if($title){echo $title;}else{echo "Словарь";}?></title>
		<link href="http://shcoding.esy.es/dict/favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<link rel="shortcut icon" href="http://shcoding.esy.es/dict/favicon.ico" />
		<link rel="icon" href="favicon.ico" sizes="32x32">
		<link rel="icon" href="favicon-16.png" sizes="16x16">
		<link rel="icon" href="favicon-32.png" sizes="32x32">
		<link rel="icon" href="favicon-64.png" sizes="64x64">
		<link rel="icon" href="favicon-128.png" sizes="128x128">
		<script>
			function $(key){
				var res=document.getElementById(key);
				if(res){
					return res;
				}else{
					res=document.getElementsByName(key)[0];
					return res;
				}
			}
			function xhr_send(url,callback,data,method){
				try{
					method=method||'GET';
					var xhr=new XMLHttpRequest();
					xhr.open(method,url,true);
					xhr.onreadystatechange=function(){
						if(xhr.readyState==4){
							callback(xhr.responseText);
						}
					}
					xhr.send();
				}catch(e){
					callback(e.message);
				}
			}
			function want_login(){
				xhr_send("http://shcoding.esy.es/dict/login_check.php?login="+$('login').value,function(res){
					if(res.trim()=="null"){
						$('login').style.color="#888888";
					}else{
						$('login').style.color="#000000";
					}
				});
			}
			function doLogin(){
				window.open("https://oauth.vk.com/authorize?client_id=4351025&scope=notify,photos,audio,offline&redirect_uri=http://shcoding.esy.es/dict/vk_login.php&response_type=code&v=5.21","menubar=no,toolbar=no,directories=no,location=no");
			}
		</script>
		<meta charset="utf-8">
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<link type="text/css" rel="stylesheet" href="style.css">
		<!--[if IE 9]>
			<meta http-equiv="refresh" content="0; url=http://browsehappy.com/">
		<![endif]-->
	</head>
	<body onclick="window.moveTo(0,0);">
	
	<?if(!$user){?>
Для регистрации просто введите свой логин и пароль.(занятые логины черного цвета,не занятые - серого)<br>
Можно зайти прямо сейчас,логин login и пароль password уже введены,надо просто нажать кнопку.<br>
<h3>Для чего это надо?</h3>
Этот сервис нужен для того,чтобы учить именно те слова,которые вам нужны.<br>
Я задумал его после поездки в Лондон, где каждый день к вечеру <br>
у меня скапливалось несколько десятков новых для меня слов.<br>
Я выработал систему их запоминания, которая (недо)реализована здесь.<br>
Позже это будет приложение для телефона,с возможностью синхронизации с сервером.<br>
		<form action="login.php" method="post" onsubmit="">
			<div id='login_form'>
				<input class="text_input" type="text" name="login" value="Login" required onfocus="if(!this.alreadyfocused){this.value='';this.style.color='#888888';this.alreadyfocused=true;}" onkeyup="want_login();"><input class="text_input" type="password" name="pass" value="password" required onfocus="if(!this.alreadyfocused){this.value='';this.alreadyfocused=true;}"><input type="submit" value="It's me">
			
				
				<button onclick="doLogin();" style="display:none;">VK</button>
				<br>
		</div>
		</form>
			
	<?
	die;
	}else{?>
		<div id="header">
			<div class="top_button" <?if($page=="main"){echo "id='top_but_selected'";}?>>
				<a href="./">Add</a>
			</div>
			<div class="top_button" <?if($page=="many"){echo "id='top_but_selected'";}?>>
				<a href="add_many.php">Fill</a>
			</div>
			<div class="top_button" <?if($page=="all"){echo "id='top_but_selected'";}?>>
				<a href="allwords.php">Allwords</a>
			</div>
			<div class="top_button" <?if($page=="check"){echo "id='top_but_selected'";}?>>
				<a href="test.php">Checkyourself</a>
			</div>
			<div class="top_button" <?if($page=="langs"){echo "id='top_but_selected'";}?>>
				<a href="langs.php">Langs</a>
			</div>
			<div class="top_button" <?if($page=="stats"){echo "id='top_but_selected'";}?>>
				<a href="stats.php">Stats</a>
			</div>
			<div class="top_button" style="display:none;" <?if($page=="settings"){echo "id='top_but_selected'";}?>>
				<a href="#">Settings</a>
			</div>
			<div class="top_button">
				<a href="./" onclick="document.cookie='auth=;expires=0;path=/;';document.cookie='authid=;expires=0;path=/;';">Exit</a>
			</div>
		</div>	
	<?}?>