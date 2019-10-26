<?php
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if(isset($_GET['nid'])){//存在nid，检验是否在tmp中，不存在就指导前端重指向
        if($nid=positive_int_filter($_GET['nid'])){
            if ($nid_exist = mysqli_fetch_row(maria($link,"select 1 from Note.note_info_tmp where nid=$nid and asdraft=1 limit 1"))[0]){
                $info = mysqli_fetch_assoc(maria($link,"
                select title,preview,catName as category,cid as categoryID,tags,inputTags,imgSrc
                from Note.note_info_tmp left join Note.note_category on catID=cid
                where nid=$nid
                limit 1
                "));
                $content = mysqli_fetch_assoc(maria($link,"select rawContent from Note.note_content_tmp where nid=$nid limit 1"));
                $info['tags'] = $info['tags']==''?[]:explode(',',$info['tags']);
                $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
                while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
                $catOptions = [];
                $res = maria($link,"select cid,catName from Note.note_category");
                while ($each = mysqli_fetch_assoc($res))$catOptions[] = $each;
                echo json_encode(['info'=>$info,'rawContent'=>$content['rawContent'],'tagOptions'=>$tagOptions,'catOptions'=>$catOptions,'exist'=>$nid_exist]);
            }
            else
                echo json_encode(['exist'=>$nid_exist]);
        }

    }
    else{ //新写文章，返回nid，并创建记录
        maria($link,"insert into Note.note_info_tmp (nid,type,lut) values (null,'note',now())");
        $nid = mysqli_insert_id($link);
        maria($link,"insert into Note.note_content_tmp (nid) values ($nid)");
//    maria($link,"insert into note_info (nid) values ($nid)");
//    maria($link,"insert into note_content (nid) values ($nid)");
        $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
        while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
        $catOptions = [];
        $res = maria($link,"select cid,catName from Note.note_category");
        while ($each = mysqli_fetch_assoc($res))$catOptions[] = $each;
        echo json_encode(['code'=>0,'tagOptions'=>$tagOptions,'catOptions'=>$catOptions,'nid'=>$nid]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}


