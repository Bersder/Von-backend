<?php //文章删除接口
require '../utils/init.php';
require '../utils/filters.php';
require '../utils/utils.php';
require '../links/secret_link.php';
$key = 'DEEPDARKFANTASY1';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
function auth_check($name,$match){
    global $link;
    $ip = get_ip();
    $ip = $ip?$ip:'Unknown IP';
    $loc = mysqli_real_escape_string($link,get_ip_loc($ip));
    maria($link,"update Tmp.auth_log set LLIp='$ip',LLLoc='$loc',LLTime=now() where typeCode=1");//记录该次授权尝试的信息

    $remain = mysqli_fetch_row(maria($link, "select remain from Tmp.auth_log where typeCode=1"))[0];//剩余次数
    if ($remain){//还有剩余次数
        $pass = mysqli_fetch_row(maria($link,"select 1 from User.me where name='$name' and match_='$match' limit 1"))[0];
        if ($pass)//授权通过
            return 0;
        else{//授权不通过
            maria($link,"update Tmp.auth_log set remain=remain-1 where typeCode=1");
            return 1;
        }
    }
    else//没有剩余次数
        return 2;
}
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if($decrypted = openssl_decrypt(base64_decode($_POST['encData']),'aes-128-cbc',$key,OPENSSL_RAW_DATA,base64_decode($_POST['param']))){
        $data = json_decode($decrypted,true);
        $name = mysqli_real_escape_string($link,$data['name']);
        $match = md5($data['psw']);
        $code = auth_check($name, $match);
        switch ($code) {
            case 0://通过密码认证开始删除文章及相关记录
                $id = $data['id'];
                $type = $data['type'];
                if ($type==='note'){
                    $delInfo = mysqli_fetch_row(maria($link,"select imgSrc from Note.note_info where nid=$id limit 1"));
                    maria($link,"delete from Tag.tag_map where xid=$id and type='note'");
                    maria($link,"delete from Tag.tag_map_tmp where xid=$id and type='note'");

                    maria($link,"delete from Comment.comment where topic_id=$id and topic_type='$type'");
                    @unlink($DISK_ROOT.$delInfo[0]);
                    @unlink($DISK_ROOT.$delInfo[0].'.thumb');
                    maria($link,"delete from Note.note_info_tmp where nid=$id limit 1");
                    maria($link,"delete from Note.note_content_tmp where nid=$id limit 1");
                    maria($link,"delete from Note.note_info where nid=$id limit 1");
                    maria($link,"delete from Note.note_content where nid=$id limit 1");
                    echo json_encode(['code'=>0],JSON_NUMERIC_CHECK);
                }
                else{
                    $delInfo = mysqli_fetch_row(maria($link,"select imgSrc from Article.article_info where aid=$id limit 1"));
                    //摸除标签
                    maria($link,"delete from Tag.tag_map where xid=$id and type<>'note'");
                    maria($link,"delete from Tag.tag_map_tmp where xid=$id and type<>'note'");
                    //摸除评论
                    maria($link,"delete from Comment.comment where topic_id=$id and topic_type='$type'");
                    @unlink($DISK_ROOT.$delInfo[0]);
                    @unlink($DISK_ROOT.$delInfo[0].'.thumb');
                    //摸除备份
                    maria($link,"delete from Article.article_info_tmp where aid=$id limit 1");
                    maria($link,"delete from Article.article_content_tmp where aid=$id limit 1");
                    //摸除本体
                    maria($link,"delete from Article.article_info where aid=$id limit 1");
                    maria($link,"delete from Article.article_content where aid=$id limit 1");
                    echo json_encode(['code'=>0],JSON_NUMERIC_CHECK);
                }
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
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1],JSON_NUMERIC_CHECK);
}



