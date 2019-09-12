<?php
require '../utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
$key = 'DEEPDARKFANTASY1';
if($decrypted = openssl_decrypt(base64_decode($_POST['encData']),'aes-128-cbc',$key,OPENSSL_RAW_DATA,base64_decode($_POST['param']))){
    $data = json_decode($decrypted,true);
    $account = $data['account'];
    $match = md5($data['psw']);
    $remember = intval($data['remember']);
    if($info = mysqli_fetch_assoc(maria($link,"select id as uid,name,avatar from User.me where (account='$account' or email='$account') and match_='$match' limit 1"))){
        $head = base64_encode(json_encode(['alg'=>'SHA256','typ'=>'JWT']));
        if ($remember)
            $payload = [
                'iss'=>'bersder3000.com',
                'uid'=>$info['id'],
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
        echo json_encode(['code'=>0,'data'=>['info'=>$info,'remember'=>$remember,'token'=>$token]]);
    }
    else{
        echo json_encode(['code'=>1]);
    }
}
else
    echo json_encode(['code'=>1]);
