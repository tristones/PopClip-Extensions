<?php 
// $username = getenv('POPCLIP_AUTH_USERNAME');
// $password = getenv('POPCLIP_AUTH_PASSWORD');
$username = "login name";
$password = "password";
$contentType = "application/x-www-form-urlencoded";
$userAgent = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4";
$body = array(
		'url'=>"http://account.youdao.com/login?service=dict&back_url=http%3A%2F%2Fdict.youdao.com&success=1",
        'product'=>"search",
        'type'=>1,
        'username'=>$username,
        'password'=>$password,
        'savelogin'=>1
    	);
$fields_string = http_build_query($body); 
 
$url = "https://reg.163.com/logins.jsp";
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
// curl_setopt($ch, CURLOPT_REFERER, "http://account.youdao.com/login?service=dict");
curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-type: '.$contentType . '; User-Agent=' . $userAgent));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_TIMEOUT, 6);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch); 
// $info = curl_getinfo($ch);
$cookies = array();
preg_match_all('/Set-Cookie:(?<cookie>.*)\b/m', $result, $cookies);
$cookie_string = trim(implode(",", $cookies['cookie']));  
curl_close($ch);

// 添加单词到单词本
$word = getenv('POPCLIP_TEXT');
$add_word_url = 'http://dict.youdao.com/wordbook/ajax?action=addword&q='.$word;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $add_word_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
// curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result2 = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code==200) {
	// 202 is accepted, 409 is already exists
	if($result2=='{"message":"adddone"}') {
		exit(0); // success
	}else{
		exit(1); // other error
	}
}  
else if ($code==401) {
	exit(2); // bad auth
}
else {
	exit(1); // other error
}
?>

