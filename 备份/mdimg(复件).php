<?php
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
$response = array();
if(isset($_FILES['hi'])&&(isset($_GET['aid'])||isset($_GET['nid']))){ //来的是header-img
    $img = $_FILES['hi'];
    $xid = isset($_GET['aid'])?$_GET['aid']:$_GET['nid'];
    $isACGN = isset($_GET['aid']);
    if(!$img['error']){
        if($img['size']<5000000){
            $type = explode('/',$img['type'])[1];
            if(in_array($type,['png','jpg','jpeg','gif','bmp','webp'])){
                $type = $type == 'jpeg'?'.jpg':'.'.$type;
                if(is_uploaded_file($img['tmp_name'])){
                    $path = '/uploads/'.date('Y/m/d');
                    if(!file_exists($DISK_ROOT.$path))mkdir($DISK_ROOT.$path,0777,true);
                    $isACGN?move_uploaded_file($img['tmp_name'],$DISK_ROOT.$path.'/HI-'.$xid.$type):move_uploaded_file($img['tmp_name'],$DISK_ROOT.$path.'/HIN-'.$xid.$type);
                    $response[] = 0;
                    $response[] = $isACGN?$path.'/HI-'.$xid.$type:$path.'/HIN-'.$xid.$type;
                }
            }
            else{//非图片类型
                $response[] = 4;
                $response[] = '/site/images/fail.jpg';
            }
        }
        else{//超出大小
            $response[] = 3;
            $response[] = '/site/images/fail.jpg';
        }
    }
    else{//传输错误
        $response[] = 2;
        $response[] = '/site/images/fail.jpg';
    }
}
elseif(isset($_FILES['img'])){ //来的是mdimg
    $img = $_FILES['img'];
    if(!$img['error']){
        if($img['size']<5000000){
            $type = explode('/',$img['type'])[1];
            if(in_array($type,['png','jpg','jpeg','gif','bmp','webp'])){
                $type = $type == 'jpeg'?'.jpg':'.'.$type;
                if(is_uploaded_file($img['tmp_name'])){
                    $md5 =  substr(md5_file($img['tmp_name']),0,16);
                    $path = '/uploads/'.date('Y/m/d');
                    if(!file_exists($DISK_ROOT.$path))mkdir($DISK_ROOT.$path,0777,true);
                    if(!file_exists($DISK_ROOT.$path.'/'.$md5.$type))move_uploaded_file($img['tmp_name'],$DISK_ROOT.$path.'/'.$md5.$type);
                    $response[] = 0;
                    $response[] = $path.'/'.$md5.$type;
                }
            }
            else{//非图片类型
                $response[] = 4;
                $response[] = '/site/images/fail.jpg';
            }
        }
        else{//超出大小
            $response[] = 3;
            $response[] = '/site/images/fail.jpg';
        }
    }
    else{//传输错误
        $response[] = 2;
        $response[] = '/site/images/fail.jpg';
    }

}
else{//没接收到img
    $response[] = 1;
    $response[] = '/site/images/fail.jpg';
}



echo json_encode($response);

