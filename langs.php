<?$page="langs";
$title="Словарь:Языки";
include "top.php";

function translit_url($text){
	preg_match_all('/./u', $text, $text);
	$text = $text[0];
	$simplePairs = array( 'а' => 'a' , 'л' => 'l' , 'у' => 'u' , 'б' => 'b' , 'м' => 'm' , 'т' => 't' , 'в' => 'v' , 'н' => 'n' , 'ы' => 'y' , 'г' => 'g' , 'о' => 'o' , 'ф' => 'f' , 'д' => 'd' , 'п' => 'p' , 'и' => 'i' , 'р' => 'r' , 'А' => 'A' , 'Л' => 'L' , 'У' => 'U' , 'Б' => 'B' , 'М' => 'M' , 'Т' => 'T' , 'В' => 'V' , 'Н' => 'N' , 'Ы' => 'Y' , 'Г' => 'G' , 'О' => 'O' , 'Ф' => 'F' , 'Д' => 'D' , 'П' => 'P' , 'И' => 'I' , 'Р' => 'R' , ); $complexPairs = array( 'з' => 'z' , 'ц' => 'c' , 'к' => 'k' , 'ж' => 'zh' , 'ч' => 'ch' , 'х' => 'kh' , 'е' => 'e' , 'с' => 's' , 'ё' => 'jo' , 'э' => 'eh' , 'ш' => 'sh' , 'й' => 'jj' , 'щ' => 'shh' , 'ю' => 'ju' , 'я' => 'ja' , 'З' => 'Z' , 'Ц' => 'C' , 'К' => 'K' , 'Ж' => 'ZH' , 'Ч' => 'CH' , 'Х' => 'KH' , 'Е' => 'E' , 'С' => 'S' , 'Ё' => 'JO' , 'Э' => 'EH' , 'Ш' => 'SH' , 'Й' => 'JJ' , 'Щ' => 'SHH' , 'Ю' => 'JU' , 'Я' => 'JA' , 'Ь' => "" , 'Ъ' => "" , 'ъ' => "" , 'ь' => "" , ); $specialSymbols = array( "_" => "-", "'" => "", "`" => "", "^" => "", " " => "-", '.' => '', ',' => '', ':' => '', '"' => '', "'" => '', '<' => '', '>' => '', '«' => '', '»' => '', ' ' => '-', ); $translitLatSymbols = array( 'a','l','u','b','m','t','v','n','y','g','o', 'f','d','p','i','r','z','c','k','e','s', 'A','L','U','B','M','T','V','N','Y','G','O', 'F','D','P','I','R','Z','C','K','E','S', ); $simplePairsFlip = array_flip($simplePairs); $complexPairsFlip = array_flip($complexPairs); $specialSymbolsFlip = array_flip($specialSymbols); $charsToTranslit = array_merge(array_keys($simplePairs),array_keys($complexPairs)); $translitTable = array(); foreach($simplePairs as $key => $val) $translitTable[$key] = $simplePairs[$key]; foreach($complexPairs as $key => $val) $translitTable[$key] = $complexPairs[$key]; foreach($specialSymbols as $key => $val) $translitTable[$key] = $specialSymbols[$key]; $result = ""; $nonTranslitArea = false; foreach($text as $char) { if(in_array($char,array_keys($specialSymbols))) { $result.= $translitTable[$char]; } elseif(in_array($char,$charsToTranslit)) { if($nonTranslitArea) { $result.= ""; $nonTranslitArea = false; } $result.= $translitTable[$char]; } else { if(!$nonTranslitArea && in_array($char,$translitLatSymbols)) { $result.= ""; $nonTranslitArea = true; } $result.= $char; } } return strtolower(preg_replace("/[-]{2,}/", '-', $result));
}

if($_POST['name']){
	$name = translit_url($_POST['name']);
	$ab=$name[0].$name[1];
	$i=2;
	$query=mysqli_query($mysqli,"SELECT * FROM dt_lang_".$user['id']);
	while($lang=mysqli_fetch_assoc($query)){
		if($lang['ab']==$ab){
			$ab.=$name[$i++];
			$query=mysqli_query($mysqli,"SELECT * FROM dt_lang_".$user['id']);
		}
	}
	echo $ab;
	$query=mysqli_query($mysqli,"INSERT INTO dt_lang_".$user['id']." VALUES('','".$_POST['name']."','".$ab."',1)");
}
?>
<script>
	function show(id){
		//Здесь надо написать козырную аякс функцию(и передавать туда статус)
		var xhr=new XMLHttpRequest();
		xhr.open('GET',"http://shcoding.esy.es/dict/lang_show.php?id="+id);
		xhr.onreadystatechange=function(){
			if(xhr.readyState==4){
				if(xhr.responseText.trim()==",ok?"){
				}else{
					console.log(xhr.responseText);
				}
			}
        }
		xhr.send();
//		document.location.href="http://shcoding.esy.es/dict/lang_show.php?id="+id;
	}
</script>
<table>
	<tr>
		<td>
			Название
		</td>
		<td>
			off / on
		</td>
		<td>
		</td>
	</tr>
<?
$query=mysqli_query($mysqli,"SELECT * FROM dt_lang_".$user['id']);
while($line=mysqli_fetch_assoc($query)){
	echo "
	<tr>
		<td>
			".$line['name']." (".$line['ab'].")
		</td>
		<td>";
			if($line['showlang']){
				echo "
			<input type='radio' onclick='show(".$line['id'].");' name='show".$line['id']."' value='0'>/<input type='radio' onclick='show(".$line['id'].");' checked name='show".$line['id']."' value='1'>
				";
			}else{
				echo "
			<input type='radio' onclick='show(".$line['id'].");' checked name='show".$line['id']."' value='0'>/<input type='radio' onclick='show(".$line['id'].");' name='show".$line['id']."' value='1'>
				";
			}
	echo "
		</td>
		<td>
			<input type='button' value='Edit' onclick='edit_lang(".$line['id'].");' style='border:1px solid red;'>
			<input type='button' value='Delete' onclick='delete_lang(".$line['id'].");' style='border:1px solid red;'>
		</td>
	</tr>";
}
?>
	<form action="langs.php" method="post">
	<tr>
		<td colspan="3">
		Добавить свой язык
	</tr>
	<tr>
		<td>
			<input type="text" name="name" value="Название">
		</td>
		<td>
			<input type="submit" value="Add">
		</td>
	</tr>
	</form>
</table>
</body>
</html>