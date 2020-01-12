<?php
require '../utils/init.php';
require '../utils/utils.php';
require '../links/secret_link.php';
$key = 'DEEPDARKFANTASY1';
function login_check($account,$match){
    global $link;
    $ip = get_ip();
    $ip = $ip?$ip:'Unknown IP';
    $loc = mysqli_real_escape_string($link,get_ip_loc($ip));
    maria($link,"update Tmp.auth_log set LLIp='$ip',LLLoc='$loc',LLTime=now() where typeCode=0");//记录该次登录尝试的信息

    $remain = mysqli_fetch_row(maria($link, "select remain from Tmp.auth_log where typeCode=0"))[0];//剩余次数
    if ($remain){//还有剩余次数
        $info = mysqli_fetch_assoc(maria($link,"select id as uid,name,avatar from User.me where (account='$account' or email='$account') and match_='$match' limit 1"));
        if ($info)//登录通过
            return [0,$info];
        else{//登录不通过
            maria($link,"update Tmp.auth_log set remain=remain-1 where typeCode=0");
            return [1,null];
        }
    }
    else//没有剩余次数
        return [2,null];
}
if($decrypted = openssl_decrypt(base64_decode($_POST['encData']),'aes-128-cbc',$key,OPENSSL_RAW_DATA,base64_decode($_POST['param']))){
    $data = json_decode($decrypted,true);
    $account = $data['account'];
    $match = md5($data['psw']);
    $remember = intval($data['remember']);
    list($code,$info) = login_check($account,$match);
    switch ($code){
        case 0:
            $head = base64_encode(json_encode(['alg'=>'SHA256','typ'=>'JWT']));
            if ($remember)
                $payload = [
                    'iss'=>'bersder3000.com',
                    'uid'=>$info['uid'],
                    'name'=>$info['name'],
                    'exp'=>time()+1209600,
                    'iat'=>time(),
                ];
            else
                $payload = [
                    'iss'=>'bersder3000.com',
                    'uid'=>$info['uid'],
                    'name'=>$info['name'],
                    'exp'=>time()+86400,
                    'iat'=>time(),
                ];
            $payload = base64_encode(json_encode($payload));
            $signature = hash_hmac('sha256',$head.'.'.$payload,'MYNAMEISVAN');
            $token = $head.'.'.$payload.'.'.$signature;
            if ($remember)
                setcookie('utk',$token,time()+1209600,'/',C_DOMAIN);
            else
                setcookie('utk',$token,0,'/',C_DOMAIN);
            echo json_encode(['code'=>0,'data'=>['info'=>$info,'remember'=>$remember,'token'=>$token]],JSON_NUMERIC_CHECK);
            break;
        case 1:
            echo json_encode(['code'=>1],JSON_NUMERIC_CHECK);
            break;
        case 2:
            echo json_encode(['code'=>2],JSON_NUMERIC_CHECK);
            break;
    }
}
else
    echo json_encode(['code'=>-1],JSON_NUMERIC_CHECK);
