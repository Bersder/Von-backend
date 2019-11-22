<?php //草稿获取
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';

if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if(isset($_POST['type'])){
        $type = $_POST['type'];
        $drafts = [];
        if ($type==='note'){
            $res = maria($link,"select nid as id,title,type,lut from Note.note_info_tmp where asdraft=1 order by lut desc");
            while ($each = mysqli_fetch_assoc($res)) $drafts[] = $each;
            echo json_encode(['code'=>0,'data'=>['drafts'=>$drafts]]);
        }
        else{
            $res = maria($link,"select aid as id,title,type,lut from Article.article_info_tmp where asdraft=1 order by lut desc");
            while ($each = mysqli_fetch_assoc($res)) $drafts[] = $each;
            echo json_encode(['code'=>0,'data'=>['drafts'=>$drafts]]);
        }
    }
    else
        echo json_encode(['code'=>1]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}

