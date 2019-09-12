<?php
function file_filter($name,$maxsize,$allowTypes=['png','jpg','jpeg','gif','bmp','webp']){
    if (isset($_FILES[$name])){
        $img = $_FILES[$name];
        $type = explode('/',$img['type'])[1];
        if (!$img['error']&&$img['size']<$maxsize&&in_array($type,$allowTypes)&&is_uploaded_file($img['tmp_name'])){
            $suffix = $type==='jpeg'?'.jpg':'.'.$type;
            $name = $img['name'];
            $md5 = md5_file($img['tmp_name']);
            return [$name,$suffix,$md5,$img['tmp_name']];
        }
        else
            return false;
    }
    else
        return false;


}
function positive_int_filter($data){
    if(preg_match('/^[1-9]\\d*$/',$data,$result))
        return intval($result[0]);
    else
        return false;
}
function in_array_filter($data,$array){
    return in_array($data,$array)?$data:false;

}
function maria_str_notnull_filter($data,$link){
    $stripped = mysqli_real_escape_string($link,trim($data));
    return  $stripped?"'".$stripped."'":false;
}


function maria_strORnull_filter($data,$link){
    $stripped = mysqli_real_escape_string($link,trim($data));
    return $stripped?"'".$stripped."'":'null';
}
function maria_pintORnull_filter($data){
    if (!$data)
        return 'null';
    elseif (preg_match('/^[1-9]\\d*$/',$data,$result))
        return intval($result[0]);
    else
        return false;
}
function maria_escape($data,$link){
    return "'".mysqli_real_escape_string($link,$data)."'";
}
function token_authorize($token){
    $tokenArray = explode('.',$token);
    if (sizeof($tokenArray)==3){
        $signature = $tokenArray[2];
        if ($signature==hash_hmac('sha256',$tokenArray[0].'.'.$tokenArray[1],'MYNAMEISVAN')){
            $info = json_decode(base64_decode($tokenArray[1]),true);
            if ($info['exp']>time()){
                return $info;
            }

        }
    }
    return false;
}