<?php //文章删除接口
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
$key = 'DEEPDARKFANTASY1';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if($decrypted = openssl_decrypt(base64_decode($_POST['encData']),'aes-128-cbc',$key,OPENSSL_RAW_DATA,base64_decode($_POST['param']))){
        $data = json_decode($decrypted,true);
        $name = $data['name'];
        $match = md5($data['psw']);
        if (mysqli_fetch_row(maria($link,"select 1 from User.me where name='$name' and match_='$match' limit 1"))[0]){//通过密码认证开始删除文章及相关记录
            $id = $data['id'];
            $type = $data['type'];
            $delTarget = ','.$id.',';
            if ($type==='note'){
                $delInfo = mysqli_fetch_row(maria($link,"select category,tags,imgSrc from Note.note_info where nid=$id limit 1"));
                if ($delCat = mysqli_real_escape_string($link,$delInfo[0]))//非空mysql比较需要转义
                    maria($link,"update Note.note_category set relateNote=replace(relateNote,'$delTarget',',') where catName_en='$delCat' limit 1");
                if ($delTags = $delInfo[1]){
                    $delTags = explode(',', $delTags);
                    foreach ($delTags as $value){
                        $escape_value = mysqli_real_escape_string($link, $value);
                        maria($link,"update Tag.tag_cloud set relateNote=replace(relateNote,'$delTarget',',') where tagName='$escape_value' limit 1");
                    }
                }
                maria($link,"delete from Comment.comment where topic_id=$id and topic_type='$type'");
                unlink($DISK_ROOT.$delInfo[2]);
                maria($link,"delete from Note.note_info_tmp where nid=$id limit 1");
                maria($link,"delete from Note.note_content_tmp where nid=$id limit 1");
                maria($link,"delete from Note.note_info where nid=$id limit 1");
                maria($link,"delete from Note.note_content where nid=$id limit 1");
                echo json_encode(['code'=>0]);
            }
            else{
                $delInfo = mysqli_fetch_row(maria($link,"select series,tags,imgSrc from Article.article_info where aid=$id limit 1"));
                if ($delSeries = $delInfo[0]){
                    $delSeries = mysqli_real_escape_string($link,$delSeries);//非空mysql比较需要转义
                    maria($link,"update Series.series_link set relateArt=replace(relateArt,'$delTarget',',') where seriesName='$delSeries' limit 1");
                }//摸除系列

                if ($delTags = $delInfo[1]){
                    $delTags = explode(',',$delTags);
                    foreach ($delTags as $value){
                        $escape_value = mysqli_real_escape_string($link, $value);
                        maria($link,"update Tag.tag_cloud set relateArt=replace(relateArt,'$delTarget',',') where tagName='$escape_value' limit 1");
                    }//摸除标签

                }
                //摸除评论
                maria($link,"delete from Comment.comment where topic_id=$id and topic_type='$type'");
                unlink($DISK_ROOT.$delInfo[2]);
                //摸除备份
                maria($link,"delete from Article.article_info_tmp where aid=$id limit 1");
                maria($link,"delete from Article.article_content_tmp where aid=$id limit 1");
                //摸除本体
                maria($link,"delete from Article.article_info where aid=$id limit 1");
                maria($link,"delete from Article.article_content where aid=$id limit 1");
                echo json_encode(['code'=>0]);
            }
            //var_dump($data);
        }
        else
            echo json_encode(['code'=>1]);
    }
    else
        echo json_encode(['code'=>1]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}



