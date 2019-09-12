<?php
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
function maria($link,$sql){
    $res = mysqli_query($link,$sql);
    if(!$res){
        echo '错误编号：'.mysqli_errno($link).'<br>';
        echo '错误信息：'.mysqli_error($link).'<br>';
        exit;
    }
    return $res;
}
maria($link,'use Note');


if(isset($_GET['nid'])){//存在nid，检验是否在tmp中，不存在就指导前端重指向
    $nid = $_GET['nid'];
    if(preg_match('/\\d+/',$nid)){
        if ($nid_exist = mysqli_fetch_row(maria($link,"select 1 from note_info_tmp where nid=$nid and asdraft=1 limit 1"))[0]){
            $info = mysqli_fetch_assoc(maria($link,"select * from note_info_tmp where nid=$nid limit 1"));
            $content = mysqli_fetch_assoc(maria($link,"select rawContent from note_content_tmp where nid=$nid limit 1"));
            $info['tags'] = $info['tags']==''?[]:explode(',',$info['tags']);
            $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
            while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
            $res = maria($link,"select catName_en,catName from note_category limit 100");$catMap = [];
            while($each =  mysqli_fetch_assoc($res))$catMap[$each['catName_en']] = $each['catName'];
            echo json_encode(['info'=>$info,'rawContent'=>$content['rawContent'],'tagOptions'=>$tagOptions,'catMap'=>$catMap,'exist'=>$nid_exist]);
        }
        else
            echo json_encode(['exist'=>$nid_exist]);

    }

}
else{ //新写文章，返回nid，并创建记录
    maria($link,"insert into note_info_tmp (nid,lut) values (null,now())");
    $nid = mysqli_insert_id($link);
    maria($link,"insert into note_content_tmp (nid) values ($nid)");
//    maria($link,"insert into note_info (nid) values ($nid)");
//    maria($link,"insert into note_content (nid) values ($nid)");


//    maria($link,'insert into note_info (nid) values (null)');
//    $nid = mysqli_insert_id($link); //严重错误，需要把自增长设置在tmp中，
//    maria($link,"insert into note_content (nid) VALUES ($nid)");
//    maria($link,"insert into note_info_tmp (nid,lut) values ($nid,now())");
//    maria($link,"insert into note_content_tmp (nid) VALUES ($nid)");
    $res = maria($link,"select tagName from Tag.tag_cloud limit 500");$tagOptions = [];
    while ($each = mysqli_fetch_assoc($res))$tagOptions[] = $each['tagName'];
    $res = maria($link,"select catName_en,catName from note_category limit 100");$catMap = [];
    while($each =  mysqli_fetch_assoc($res))$catMap[$each['catName_en']] = $each['catName'];
    echo json_encode(['tagOptions'=>$tagOptions,'catMap'=>$catMap,'nid'=>$nid]);
}

