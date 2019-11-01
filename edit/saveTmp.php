<?php
require '../utils/init.php';
require '../utils/filters.php';
require '../links/limit_link.php';
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if(isset($_GET['aid'])&&($type=in_array_filter($_POST['type'],['anime','code','game','trivial']))&&($aid=positive_int_filter($_GET['aid']))){
        $author = maria_escape($_POST['author'],$link) ;
        $preview = maria_escape($_POST['preview'],$link) ;
        $rawContent = maria_escape($_POST['rawContent'],$link) ;
        $seriesID = maria_strORnull_filter($_POST['seriesID'],$link);
        $tagsID = $_POST['tagsID'];
        $inputTags = maria_escape($_POST['inputTags'],$link) ;
        $title = maria_escape($_POST['title'],$link) ;

        foreach (explode(',',$tagsID) as $value){//不管怎样先建立当前草稿标签关系，删除的后面判断
            if ($value=positive_int_filter($value)){
                maria($link,"insert ignore into Tag.tag_map_tmp values($aid,'$type',$value)");
                //var_dump($value);
            }
        }
        $oldType = mysqli_fetch_row(maria($link,"select type from Article.article_info_tmp where aid=$aid limit 1"))[0];
        $oldType_ = maria_escape($oldType,$link);
        if ($oldType!=$type){//如果修改了type，要把旧的全删除
            maria($link,"delete from Tag.tag_map_tmp where xid=$aid and type=$oldType_");
        }
        else{
            $oldTagsID_tmp = explode(',',mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc_tmp where xid=$aid and type<>'note'"))[0]);
            $deleteTagsID_tmp = array_diff($oldTagsID_tmp,explode(',',$tagsID));
            //var_dump($deleteTagsID_tmp);
            foreach ($deleteTagsID_tmp as $value){ //标签删除，和数据库比较取得被删除的标签，然后删除对应标签下的id
                maria($link,"delete from Tag.tag_map_tmp where xid=$aid and type<>'note' and tid=$value");
            }
        }

        maria($link,"update Article.article_info_tmp set author=$author,preview=$preview,seriesID=$seriesID,inputTags=$inputTags,title=$title,type='$type',lut=now() where aid=$aid limit 1");
        maria($link,"update Article.article_content_tmp set rawContent=$rawContent where aid=$aid limit 1");
        echo json_encode(['code'=>0]);
    }
    elseif(isset($_GET['nid'])&&$_POST['type']==='note'&&($nid=positive_int_filter($_GET['nid']))&&($catID=positive_int_filter($_POST['categoryID']))){
        $author = maria_escape($_POST['author'],$link) ;
        $preview = maria_escape($_POST['preview'],$link) ;
        $rawContent = maria_escape($_POST['rawContent'],$link) ;
        $tagsID = $_POST['tagsID'];
        $inputTags = maria_escape($_POST['inputTags'],$link) ;
        $title = maria_escape($_POST['title'],$link) ;

        foreach (explode(',',$tagsID) as $value){//建立草稿标签关系
            if ($value=positive_int_filter($value)){
                maria($link,"insert ignore into Tag.tag_map_tmp values($nid,'note',$value)");
                //var_dump($value);
            }
        }
        $oldTagsID_tmp = explode(',',mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc_tmp where xid=$nid and type='note'"))[0]);
        $deleteTagsID_tmp = array_diff($oldTagsID_tmp,explode(',',$tagsID));
        //var_dump($deleteTagsID_tmp);
        foreach ($deleteTagsID_tmp as $value){
            maria($link,"delete from Tag.tag_map_tmp where xid=$nid and type='note' and tid=$value");
        }

        maria($link,"update Note.note_info_tmp set author=$author,preview=$preview,catID=$catID,inputTags=$inputTags,title=$title,type='note',lut=now() where nid=$nid limit 1");
        maria($link,"update Note.note_content_tmp set rawContent=$rawContent where nid=$nid limit 1");
        echo json_encode(['code'=>0]);
    }
    else{//没有aid/aid非法/不对应
        echo json_encode(['code'=>1]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}
