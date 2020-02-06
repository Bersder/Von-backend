<?php //增删动态、图片上载、安利
require '../utils/init.php';
require '../utils/filters.php';
require '../utils/utils.php';
require '../links/secret_link.php';
$link->autocommit(false);
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];

if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    $s = isset($_POST['service_type']) ? intval($_POST['service_type']) : 0;
    switch ($s){
        case 1://图片上载
            if($imgInfo=file_filter('img_up',10485760,['png','jpg','jpeg','gif'])){
                $path = '/tmp/'.substr($imgInfo[2],0,16).substr(md5(time()),0,16).$imgInfo[1];
                move_uploaded_file($imgInfo[3],$DISK_ROOT.$path);
                if (thumb_img($DISK_ROOT.$path)){}
                else
                    copy($DISK_ROOT.$path,$DISK_ROOT.$path.'.thumb');
                echo json_encode(['code'=>0,'data'=>['imgSrc'=>$path]],JSON_NUMERIC_CHECK);
            }
            else
                echo json_encode(['code' => 1],JSON_NUMERIC_CHECK);
            break;
        case 2://取消暂存
            $path = isset($_POST['path'])?$_POST['path']:'';
            if (preg_match('/^\/tmp\/.+/',$path)){
                @unlink($DISK_ROOT.$path);
                @unlink($DISK_ROOT.$path.'.thumb');
                echo json_encode(['code' => 0],JSON_NUMERIC_CHECK);
            }
            else
                echo json_encode(['code' => 1],JSON_NUMERIC_CHECK);
            break;
        case 3://动态发布
            if ($content=maria_str_notnull_filter($_POST['content'],$link)){
                $dType = isset($_POST['d_type']) ? intval($_POST['d_type']) : 0;
                $imgsString = isset($_POST['imgs_string']) ? trim($_POST['imgs_string'],',') : '';
                if ($imgsString){//有图片的话移动暂存图片
                    $imgsPaths = explode(',',$imgsString);
                    foreach ($imgsPaths as $value){//检查暂存图片是否都存在
                        if (!file_exists($DISK_ROOT.$value)){
                            echo json_encode(['code' => 2,'message'=>'imgs lost'],JSON_NUMERIC_CHECK);
                            die();
                        }
                    }
                    foreach ($imgsPaths as $value){//移动暂存图片
                        $path = escapeshellarg($value);
                        exec("mv {$DISK_ROOT}{$path}* {$DISK_ROOT}/uploads/dalbum");
                    }
                }
                //var_dump($content,$dType,$imgsString);
                $imgsString = maria_strORnull_filter(str_replace('tmp','uploads/dalbum',$imgsString),$link);
                maria($link,"insert into Dynamic.dyn_record (type, content, imgs, time) values ($dType,$content,$imgsString,now())");
                $did = mysqli_insert_id($link);
                $link->commit();
                //返回新动态信息
                list($author,$avatar) = mysqli_fetch_row(maria($link,"select uname,avatar from User.user where id=1"));
                $dynamic = mysqli_fetch_assoc(maria($link,"select * from Dynamic.dyn_record where id=$did limit 1"));
                $dynamic['author'] = $author;
                $dynamic['avatar'] = $avatar;
                $dynamic['imgs'] = $dynamic['imgs']?explode(',',$dynamic['imgs']):[];
                echo json_encode(['code' => 0,'data'=>['dynamic'=>$dynamic]]);
            }
            else
                echo json_encode(['code' => 1],JSON_NUMERIC_CHECK);
            break;
        case 4://动态删除
            if ($delID = positive_int_filter($_POST['delID'])){
                $imgsString = mysqli_fetch_row(maria($link,"select imgs from Dynamic.dyn_record where id=$delID limit 1"))[0];
                if ($imgsString){//如果有图片进行删除
                    $imgsPaths = explode(',',$imgsString);
                    foreach ($imgsPaths as $value){
                        @unlink($DISK_ROOT.$value);
                        @unlink($DISK_ROOT.$value.'.thumb');
                    }
                }
                //删除动态及相关评论及喜欢记录
                maria($link,"delete from Dynamic.dyn_record where id=$delID limit 1");
                maria($link,"delete from Dynamic.dyn_comment where dyn_id=$delID");
                maria($link,"delete from Dynamic.dyn_like_log where dyn_id=$delID");
                $link->commit();
                echo json_encode(['code' => 0],JSON_NUMERIC_CHECK);
            }
            else
                echo json_encode(['code' => 1],JSON_NUMERIC_CHECK);
            break;
        case 5://安利
            if (($url=maria_str_notnull_filter($_POST['url'],$link))&&($title=maria_str_notnull_filter($_POST['title'],$link))){
                maria($link,"insert into Dynamic.recommend values (null, $title, $url,now())");
                $link->commit();
                echo json_encode(['code' => 0],JSON_NUMERIC_CHECK);
            }
            else
                echo json_encode(['code' => 1],JSON_NUMERIC_CHECK);
            break;
        default:
            echo json_encode(['code' => -1],JSON_NUMERIC_CHECK);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}