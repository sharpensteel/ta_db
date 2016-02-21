<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Curl_utils
 *
 * @author Moss
 */
class Curl_utils {
	static public $CURLOPT_NAME_ARR = array(
		58 => 'CURLOPT_AUTOREFERER',
		19914 => 'CURLOPT_BINARYTRANSFER',
		98 => 'CURLOPT_BUFFERSIZE',
		10065 => 'CURLOPT_CAINFO',
		10097 => 'CURLOPT_CAPATH',
		72 => 'CURLOPT_CLOSEPOLICY',
		78 => 'CURLOPT_CONNECTTIMEOUT',
		10022 => 'CURLOPT_COOKIE',
		10031 => 'CURLOPT_COOKIEFILE',
		10082 => 'CURLOPT_COOKIEJAR',
		96 => 'CURLOPT_COOKIESESSION',
		27 => 'CURLOPT_CRLF',
		10036 => 'CURLOPT_CUSTOMREQUEST',
		92 => 'CURLOPT_DNS_CACHE_TIMEOUT',
		91 => 'CURLOPT_DNS_USE_GLOBAL_CACHE',
		10077 => 'CURLOPT_EGDSOCKET',
		10102 => 'CURLOPT_ENCODING',
		45 => 'CURLOPT_FAILONERROR',
		10001 => 'CURLOPT_FILE',
		69 => 'CURLOPT_FILETIME',
		52 => 'CURLOPT_FOLLOWLOCATION',
		75 => 'CURLOPT_FORBID_REUSE',
		74 => 'CURLOPT_FRESH_CONNECT',
		50 => 'CURLOPT_FTPAPPEND',
		48 => 'CURLOPT_FTPLISTONLY',
		10017 => 'CURLOPT_FTPPORT',
		106 => 'CURLOPT_FTP_USE_EPRT',
		85 => 'CURLOPT_FTP_USE_EPSV',
		42 => 'CURLOPT_HEADER',
		20079 => 'CURLOPT_HEADERFUNCTION',
		10104 => 'CURLOPT_HTTP200ALIASES',
		80 => 'CURLOPT_HTTPGET',
		10023 => 'CURLOPT_HTTPHEADER',
		61 => 'CURLOPT_HTTPPROXYTUNNEL',
		84 => 'CURLOPT_HTTP_VERSION',
		10009 => 'CURLOPT_INFILE',
		14 => 'CURLOPT_INFILESIZE',
		10062 => 'CURLOPT_INTERFACE',
		10063 => 'CURLOPT_KRB4LEVEL',
		19 => 'CURLOPT_LOW_SPEED_LIMIT',
		20 => 'CURLOPT_LOW_SPEED_TIME',
		71 => 'CURLOPT_MAXCONNECTS',
		68 => 'CURLOPT_MAXREDIRS',
		51 => 'CURLOPT_NETRC',
		44 => 'CURLOPT_NOBODY',
		43 => 'CURLOPT_NOPROGRESS',
		99 => 'CURLOPT_NOSIGNAL',
		3 => 'CURLOPT_PORT',
		47 => 'CURLOPT_POST',
		10015 => 'CURLOPT_POSTFIELDS',
		10039 => 'CURLOPT_POSTQUOTE',
		10093 => 'CURLOPT_PREQUOTE',
		20056 => 'CURLOPT_PROGRESSFUNCTION',
		10004 => 'CURLOPT_PROXY',
		59 => 'CURLOPT_PROXYPORT',
		101 => 'CURLOPT_PROXYTYPE',
		10006 => 'CURLOPT_PROXYUSERPWD',
		54 => 'CURLOPT_PUT',
		10028 => 'CURLOPT_QUOTE',
		10076 => 'CURLOPT_RANDOM_FILE',
		10007 => 'CURLOPT_RANGE',
		10009 => 'CURLOPT_READDATA',
		20012 => 'CURLOPT_READFUNCTION',
		10016 => 'CURLOPT_REFERER',
		21 => 'CURLOPT_RESUME_FROM',
		19913 => 'CURLOPT_RETURNTRANSFER',
		10100 => 'CURLOPT_SHARE',
		10025 => 'CURLOPT_SSLCERT',
		10026 => 'CURLOPT_SSLCERTPASSWD',
		10086 => 'CURLOPT_SSLCERTTYPE',
		10089 => 'CURLOPT_SSLENGINE',
		90 => 'CURLOPT_SSLENGINE_DEFAULT',
		10087 => 'CURLOPT_SSLKEY',
		10026 => 'CURLOPT_SSLKEYPASSWD',
		10088 => 'CURLOPT_SSLKEYTYPE',
		32 => 'CURLOPT_SSLVERSION',
		10083 => 'CURLOPT_SSL_CIPHER_LIST',
		81 => 'CURLOPT_SSL_VERIFYHOST',
		64 => 'CURLOPT_SSL_VERIFYPEER',
		10037 => 'CURLOPT_STDERR',
		10070 => 'CURLOPT_TELNETOPTIONS',
		33 => 'CURLOPT_TIMECONDITION',
		13 => 'CURLOPT_TIMEOUT',
		34 => 'CURLOPT_TIMEVALUE',
		53 => 'CURLOPT_TRANSFERTEXT',
		105 => 'CURLOPT_UNRESTRICTED_AUTH',
		46 => 'CURLOPT_UPLOAD',
		10002 => 'CURLOPT_URL',
		10018 => 'CURLOPT_USERAGENT',
		10005 => 'CURLOPT_USERPWD',
		41 => 'CURLOPT_VERBOSE',
		20011 => 'CURLOPT_WRITEFUNCTION',
		10029 => 'CURLOPT_WRITEHEADER',

	);
	
	static public function get_curlopt_name($curlopt_id){
		return isset(self::$CURLOPT_NAME_ARR[$curlopt_id]) ? self::$CURLOPT_NAME_ARR[$curlopt_id] : "CUROPT#".$curlopt_id;
	}
			
	static public function curl_wrapper($ch, $curlopt_arr = array()){
		$ch = curl_init();
		
		$curlopt_arr[CURLOPT_HEADER] = 1;
		$curlopt_arr[CURLOPT_RETURNTRANSFER] = 1;
		

		
		$curlopt_named_arr = array();
		
		foreach ($curlopt_arr as $key => $val){
			$curlopt_named_arr[self::get_curlopt_name($key)] = $val;
			curl_setopt($ch, $key, $val);
		};
		// загрузка страницы и выдача её браузеру
		$response = curl_exec($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$error_str =""; $ok=1;
		if(curl_errno($ch))
		{
			$error_str .= curl_error($ch).' ';
			$ok = 0;
		}
		
		

		?>
		<br><br>
		<b>params:</b> <? var_dump($curlopt_named_arr); ?><br><br>
		<b>status:</b> <?=$status ?><br><br>
		<? if(!$ok){ ?>Curl error: <?=$error_str?><? } ?><br>
		<b>response header:</b><br><pre><?=htmlspecialchars($header); ?></pre><br><br>
		<b>response body:</b><br><pre><?=htmlspecialchars($body); ?></pre><br><br>
		<?

		$response = array(
			'http_status' => $status,
			'error_str' => $error_str,
			'response_header' => $header,
			'response_body' => $body
		);
		
		return $response;
		
	}
	
}
