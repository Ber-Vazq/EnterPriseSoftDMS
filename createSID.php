<?php
$page="createSID";
$username="jkg215";
$password="Fc*BmKjMdZY8!bpW";
$data="username=$username&password=$password";
$ch=curl_init('https://cs4743.professorvaladez.com/api/create_session');
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'content-type: application/x-www-form-urlencoded',
    'content-length: '. strlen($data)));
$time_start=microtime(true);
$result=curl_exec($ch);
$time_end=microtime(true);
$execution_time=($time_end-$time_start)/60;
$cinfo=json_decode($result, true);
echo "<pre>";
print_r($cinfo);
echo "</pre>";
?>