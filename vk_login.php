<?include "../mysql.php";
error_reporting(E_ALL);

/* Позволить сценарию зависнуть вокруг ожидания подключений */
set_time_limit(0);

/* Включить неявный вывод, так что мы видим то, что мы получаем
 * когда это приходит . */
ob_implicit_flush();


function set_cookie($name,$value,$expire,$path){
	echo "
	<script>
		var curCookie='".$name."=".$value.";expires=".$expire.";path=".$path."';
		document.cookie=curCookie;
	</script>";
	return true;
}

if($_GET['code']){
	$data=file_get_contents('https://oauth.vk.com/access_token?client_id=4351025&client_secret=3BeggHPMZdBXBtNSMMpb&code='.$_GET['code'].'&redirect_uri=http://shcoding.esy.es/dict/vk_login.php');
	$data=json_decode($data);
	print_r($data);
	if($data->user_id){
		$hash=$data->access_token;
		$query=mysql_query("SELECT * FROM dt_users WHERE vk_id=".$data->user_id);
		if($user=mysql_fetch_assoc($query)){
		//
			mysql_query("UPDATE dt_users SET hash='".$hash."' where id=".$user['id']);
			set_cookie("authid",$user['id'],time()+(60*60*24*2),"/");
			set_cookie("auth",$hash,time()+(60*60*24*7),"/");
			//Header("Location: http://shcoding.esy.es/dict/");
			echo "С возвращением!";
		}else{
			echo "Create new user<br>";
			$link="https://api.vk.com/method/users.get.XML?user_ids=".$data->user_id."&v=5.21&access_token=".$hash;
	
set_cookie("authid",$data->user_id,time()+(60*60*24*2),'/');
set_cookie("auth",$hash,time()+(60*60*24*7),'/');
						
			$vk_user=file($link);
			echo $vk_user;
			print_r($vk_user);
			$vk_user=json_decode($vk_user);
			print_r($vk_user);
			echo $vk_user->first_name." ".$vk_user->last_name;
			echo "<br><a href='".$link."'>".$link."</a><br>";			
		}	
		echo mysql_error();
/*			mysql_query("INSERT INTO dt_users VALUES ('','0','".$_POST['login']."','".md5("ghjcnjcjkm".md5($_POST['pass']))."','".$hash."')");
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
			echo "Send - ".$sent;
		}
		?>
	<script>
		document.location.href='<?echo $_SERVER['HTTP_REFERER'];?>';
	</script>
		<?
*/		
	}else{
		echo $data->error."<br>";
		echo $data->error_description;
	}
}else{
	echo $_GET['error']."<br>".$_GET['error_description'];
	
	$link="https://api.vk.com/method/users.get?user_ids=".$_COOKIE['authid']."&v=5.21&access_token=".$_COOKIE['auth'];

	echo 11;
$curl = curl_init();
//echo $curl;
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17'); 
curl_setopt($curl, CURLOPT_URL, $link);
$response = curl_exec($curl);
curl_close($curl);
echo $response;


	echo $vk_user;
	print_r($vk_user);
	echo 22;
	$vk_user=json_decode($vk_user);
	print_r($vk_user);
	echo $vk_user->first_name." ".$vk_user->last_name;
	echo "<br><a href='".$link."'>".$link."</a><br>";
	
}
?>