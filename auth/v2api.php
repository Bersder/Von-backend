<?php //舍弃/创建草稿
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';

if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if(($id = positive_int_filter($_POST['id']))&&($type = in_array_filter($_POST['type'],['article','note','anime','code','game','trivial']))){
        $flag = isset($_GET['create'])?1:0;
        if ($type==='note'){
            if($launched = mysqli_fetch_row(maria($link,"select 1 from Note.note_info where nid=$id limit 1"))[0])
                maria($link,"update Note.note_info_tmp set asdraft=$flag where nid=$id limit 1");
            else{
                maria($link,"delete from Note.note_info_tmp where nid=$id limit 1");
                maria($link,"delete from Note.note_content_tmp where nid=$id limit 1");
            }
        }

        else{
            if($launched = mysqli_fetch_row(maria($link,"select 1 from Article.article_info where aid=$id limit 1"))[0])
                maria($link,"update Article.article_info_tmp set asdraft=$flag where aid=$id limit 1");
            else{
                maria($link,"delete from Article.article_info_tmp where aid=$id limit 1");
                maria($link,"delete from Article.article_content_tmp where aid=$id limit 1");
            }
        }

        echo json_encode(['code'=>0]);

    }
    else
        echo json_encode(['code'=>1]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}

