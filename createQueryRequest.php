<?php
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
curl_close($ch);
$cinfo=json_decode($result, true);
//checking current status
if($cinfo[0]=="Status: OK")
{
    $sid=$cinfo[2];
    $data="uid=$username&sid=$sid";
    $ch=curl_init('https://cs4743.professorvaladez.com/api/query_files');
    //echo "<h3>Payload for query_files: $data</h3>";
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
     'content-type: application/x-www-form-urlencoded',
     'content-length: '. strlen($data)));
    $time_start=microtime(true);
    $result=curl_exec($ch);
    $time_end=microtime(true);
    $execTime=($time_end-$time_start)/60;
    curl_close($ch);
    $cinfo=json_decode($result,true);
    echo "<pre>";
    print_r($cinfo);
    echo "</pre>";
    $tmp=explode(":",$cinfo[1]);
    $payload=json_decode($tmp[1]);
    foreach($payload as $key=>$value)
    {
        
       $data="sid=$sid&uid=$username&fid=$value"; $ch=curl_init('https://cs4743.professorvaladez.com/api/request_file');
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'content-type: application/x-www-form-urlencoded',
         'content-length: '. strlen($data)));
        $time_start=microtime(true);
        $result=curl_exec($ch);
        $time_end=microtime(true);
        $execTime=($time_end-$time_start)/60;
        curl_close($ch);
        if (strstr($result,"Status"))
        {
            echo "<h2> there w an error with file: $value</h2>";
            echo "<pre>";
            echo $result;
            echo "</pre>";
            continue;
        }
        else
        {
            $content=$result;
            if(strlen($content)==0)
            {
                echo "<h2>File $value received zero length</h2>";
                continue;
            }
            else
            {
                $fp=fopen("/var/www/html/files/$value","wb");
                fwrite($fp,$content);
                fclose($fp);
                echo "<h3>File $value written to filesystem</h3>";
            }
        }
    }
    
    $data="sid=$sid"; $ch=curl_init('https://cs4743.professorvaladez.com/api/close_session');
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
     'content-type: application/x-www-form-urlencoded',
     'content-length: '. strlen($data)));
    curl_exec($ch);
    echo "<h3>Session Closed. Done </h3>";
}
else{
    echo "<pre>";
    print_r($cinfo);
    echo "</pre>";
}
?>
