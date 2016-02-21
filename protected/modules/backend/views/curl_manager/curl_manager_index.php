<?
$ok = 1;
$error_str = '';

$url = isset($_REQUEST['curl_manager']) ? $_REQUEST['curl_manager']['url'] : 'https://www.tiberiumalliances.com/j_security_check';
$input_body = isset($_REQUEST['curl_manager']) ? array_default($_REQUEST['curl_manager'],'post_data') : '&id=&j_username=&password=&spring-security-redirect=&timezone=0';
$input_headers = isset($_REQUEST['curl_manager']) ? array_default($_REQUEST['curl_manager'],'input_headers') : '';
$input_cookies = isset($_REQUEST['curl_manager']) ? array_default($_REQUEST['curl_manager'],'input_cookies') : '';
$is_post = isset($_REQUEST['curl_manager']) ? isset($_REQUEST['curl_manager']['is_post']) : 1;

	
//&id=&j_username=sharpensteel@yandex.ru&password=Rzhev1942&spring-security-redirect=&timezone=0
?>
curl manager<br>
<?


if(!function_exists('curl_init')){
	throw new Exception("curl not enabled!!");
}


// создание нового ресурса cURL
$ch = curl_init();

// установка URL и других необходимых параметров
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, $is_post ? 1 : 0);
if($is_post){
	curl_setopt($ch, CURLOPT_POSTFIELDS, $input_body);
}

if(strlen($input_cookies)){
	curl_setopt($ch, CURLOPT_COOKIE, $input_cookies);
}

if(strlen($input_headers)){
	curl_setopt($ch, CURLOPT_HTTPHEADER, array( $input_headers ));
}


if(isset($GLOBALS['SSL_CERTIFICATE_PATH'])){
	curl_setopt ($ch, CURLOPT_CAINFO, $GLOBALS['SSL_CERTIFICATE_PATH']);
}

//JSESSIONID=(\w+)\W


// загрузка страницы и выдача её браузеру
$response = curl_exec($ch);

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if(curl_errno($ch))
{
	$error_str .= 'Curl error: ' . curl_error($ch).' ';
	$ok = 0;
}

// завершение сеанса и освобождение ресурсов
curl_close($ch);

/*You need this :

1. POST request at htxps://www.tiberiumalliances.com/j_security_check
with : j_password,j_username,spring-security-redirect,timezone,web_remember_me
to get the JSESSIONID cookie.

2.GET request at htxps://www.tiberiumalliances.com/game/worldBrowser with the JSESSIONID cookie to get the sessionID wich you will match with regex from response

3.POST request at ../ajaxEndpoint/OpenSession with :
session,reset,refId,version,platformId to get final session id

To get Alliance Info you post a json request {"session": session id, "id": allianceID} to ../ajaxEndpoint/GetPublicAllianceInfo
same for Player Info but send request to ../ajaxEndpoint/GetPublicPlayerInfo

Use the Developer Tools in Firefox or Chrome to monitor network request & responses from server when you login , click on change server under Play Button , and also when you click Play and game loads look at request made to ../ajaxEndpoint/OpenSession
and in game you can get all the info you want by clicking buttons and see what apear in browser webconsole. */

?>
<form action="<?=createUrl('backend/curl_manager')?>" method="POST">
	<br>
	
	url:<br><input type="text" name="curl_manager[url]" value="<?=$url ?>" style="width:600px;"><br><br>
	post_data:<br><textarea type="text" name="curl_manager[input_body]" style="width:600px;height:100px;"><?=$input_body ?></textarea><br><br>
	headers:<br><textarea type="text" name="curl_manager[input_headers]" style="width:600px;height:100px;"><?=$input_headers ?></textarea><br><br>
	cookies:<br><textarea type="text" name="curl_manager[input_cookies]" style="width:600px;height:100px;"><?=$input_cookies ?></textarea><br><br>
	is POST:<input type="checkbox" name="curl_manager[is_post]" <?=$is_post?'checked':'' ?> ><br><br>
	<input type="submit" value="submit">
</form>

<br><br>
<b>url:</b> <?=htmlspecialchars($url); ?><br><br>
<b>status:</b> <?=$status ?><br><br>
<? if(!$ok){ ?>error: <?=$error_str?><? } ?>
<b>responce header:</b><br><pre><?=htmlspecialchars($header); ?></pre><br><br>
<b>responce body:</b><br><pre><?=htmlspecialchars($body); ?></pre><br><br>



