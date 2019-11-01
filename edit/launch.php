<?php
require '../utils/init.php';
require '../utils/filters.php';
require '../links/limit_link.php';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];

function add_newTags($link){
    $newTagsID = [];
    if(isset($_POST['newTags'])&&$_POST['newTags']){//有新标签时，添加至标签云
        $newTags = $_POST['newTags'];
        foreach (explode(',',$newTags) as $value){
            $escape_value = maria_escape($value,$link);
            if(!mysqli_fetch_row(maria($link,"select 1 from Tag.tag_cloud where tagName=$escape_value limit 1"))[0]){
                maria($link,"insert into Tag.tag_cloud (tid,tagName) values(null,$escape_value)");
                $newTagsID[] = strval(mysqli_insert_id($link));
            }
        }
    }
    if (sizeof($newTagsID))//返回新标签的id共之后添加
        return ','.implode(',',$newTagsID);
    else
        return '';
}

if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if(isset($_GET['aid'])&&in_array($_POST['type'],['anime','code','game','trivial'])){
        maria($link,'use Article');
        if($aid=positive_int_filter($_GET['aid'])){
            $type = $_POST['type'];
            $isFirst = !(mysqli_fetch_row(maria($link,"select 1 from article_info where aid=$aid limit 1"))[0]); //可能修改

            $imgSrc = mysqli_real_escape_string($link,$_POST['imgSrc']);//转义中
            $author = mysqli_real_escape_string($link,$_POST['author']);
            $preview = mysqli_real_escape_string($link,$_POST['preview']);
            $rawContent = mysqli_real_escape_string($link,$_POST['rawContent']);
            $seriesID = maria_strORnull_filter($_POST['seriesID'],$link);
            $title = mysqli_real_escape_string($link,$_POST['title']);

            $tagsID = $_POST['tagsID'] . add_newTags($link);
            foreach (explode(',',$tagsID) as $value){//不管怎样先建立当前草稿标签关系，删除的后面判断
                if ($value=positive_int_filter($value)){
                    maria($link,"insert ignore into Tag.tag_map values($aid,'$type',$value)");
                    maria($link,"insert ignore into Tag.tag_map_tmp values($aid,'$type',$value)");
                }
            }

            //然后录入文章信息
            if($isFirst){ //首次发布，往非tmp中插入新行、设置草稿备份
                maria($link,"insert into article_info  (aid, type, title, preview, imgSrc, author, time, lut, seriesID) values($aid,'$type','$title','$preview','$imgSrc','$author',now(),now(),$seriesID)");
                maria($link,"insert into article_content (aid, rawContent) values($aid,'$rawContent')");
                maria($link,"update article_info_tmp set asbu=1,time=now() where aid=$aid limit 1");
                //var_dump('first');
            }
            else{//二次更改发布
                $oldType = mysqli_fetch_row(maria($link,"select type from Article.article_info where aid=$aid limit 1"))[0];
                $oldType_tmp = mysqli_fetch_row(maria($link,"select type from Article.article_info_tmp where aid=$aid limit 1"))[0];
                $oldType_ = maria_escape($oldType,$link);
                $oldType_tmp_ = maria_escape($oldType_tmp,$link);
                $tagsID_ = explode(',',$tagsID);

                if ($oldType!=$type){//如果type发生了改变，需要把旧的删除
                    maria($link,"delete from Tag.tag_map where xid=$aid and type=$oldType_");
                }
                else{
                    $oldTagsID = explode(',',mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc where xid=$aid and type<>'note'"))[0]);
                    $deleteTagsID = array_diff($oldTagsID,$tagsID_);
                    foreach ($deleteTagsID as $value){ //标签删除，和数据库比较取得被删除的标签，然后删除对应标签下的id
                        maria($link,"delete from Tag.tag_map where xid=$aid and type<>'note' and tid=$value");
                    }
                }
                if ($oldType_tmp!=$type){
                    maria($link,"delete from Tag.tag_map_tmp where xid=$aid and type=$oldType_tmp_");
                }
                else{
                    $oldTagsID_tmp = explode(',',mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc_tmp where xid=$aid and type<>'note'"))[0]);
                    $deleteTagsID_tmp = array_diff($oldTagsID_tmp,$tagsID_);
                    foreach ($deleteTagsID_tmp as $value){ //标签删除，和数据库比较取得被删除的标签，然后删除对应标签下的id
                        maria($link,"delete from Tag.tag_map_tmp where xid=$aid and type<>'note' and tid=$value");
                    }
                }

                $oldImg = mysqli_fetch_row(maria($link,"select imgSrc from article_info where aid=$aid limit 1"))[0];//旧图删除
                if($oldImg!=$imgSrc){
                    unlink($DISK_ROOT.$oldImg);
                    @unlink($DISK_ROOT.$oldImg.'.thumb');
                }//避免同名不同格式
                maria($link,"update article_info set author='$author',imgSrc='$imgSrc',preview='$preview',seriesID=$seriesID,title='$title',type='$type',lut=now() where aid=$aid limit 1");
                maria($link,"update article_content set rawContent='$rawContent' where aid=$aid limit 1");
                //var_dump('second');
            }
            //草稿跟随发布更新，并取消草稿
            maria($link,"update article_info_tmp set author='$author',imgSrc='$imgSrc',preview='$preview',seriesID=$seriesID,inputTags='',title='$title',type='$type',lut=now(),asdraft=0 where aid=$aid limit 1");
            maria($link,"update article_content_tmp set rawContent='$rawContent'where aid=$aid limit 1");
            echo json_encode(['code'=>0]);
        }
    }
    elseif(isset($_GET['nid'])&&$_POST['type']=='note'){
        maria($link,'use Note');
        if(($nid=positive_int_filter($_GET['nid']))&&($catID=positive_int_filter($_POST['categoryID']))){
            $type = 'note';
            $isFirst = !(mysqli_fetch_row(maria($link,"select 1 from note_info where nid=$nid limit 1"))[0]);

            $imgSrc = mysqli_real_escape_string($link,$_POST['imgSrc']); //转义中
            $author = mysqli_real_escape_string($link,$_POST['author']);
            $preview = mysqli_real_escape_string($link,$_POST['preview']);
            $rawContent = mysqli_real_escape_string($link,$_POST['rawContent']);
            $title = mysqli_real_escape_string($link,$_POST['title']);

            $tagsID = $_POST['tagsID'] . add_newTags($link);
            foreach (explode(',',$tagsID) as $value){//创建文章与标签联系,删除的等下面
                if ($value=positive_int_filter($value)){
                    maria($link,"insert ignore into Tag.tag_map values($nid,'note',$value)");
                    maria($link,"insert ignore into Tag.tag_map_tmp values($nid,'note',$value)");
                }
            }

            if($isFirst){
                maria($link,"insert into note_info (nid, type, title, preview, imgSrc, author, time, lut, catID) values ($nid,'note','$title','$preview','$imgSrc','$author',now(),now(),$catID)");
                maria($link,"insert into note_content (nid, rawContent) values ($nid,'$rawContent')");
                maria($link,"update note_info_tmp set asbu=1,time=now() where nid=$nid limit 1");
                //var_dump('first');
            }
            else{
                $tagsID_ = explode(',',$tagsID);
                $oldTagsID = explode(',',mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc where xid=$nid and type='note'"))[0]);
                $deleteTagsID = array_diff($oldTagsID,$tagsID_);
                foreach ($deleteTagsID as $value){ //标签删除，和数据库比较取得被删除的标签
                    maria($link,"delete from Tag.tag_map where xid=$nid and type='note' and tid=$value");
                }
                $oldTagsID_tmp = explode(',',mysqli_fetch_row(maria($link,"select group_concat(tid) as tagsID from Tag.tm_tc_tmp where xid=$nid and type='note'"))[0]);
                $deleteTagsID_tmp = array_diff($oldTagsID_tmp,$tagsID_);
                foreach ($deleteTagsID_tmp as $value){
                    maria($link,"delete from Tag.tag_map_tmp where xid=$nid and type='note' and tid=$value");
                }
                $oldImg = mysqli_fetch_row(maria($link,"select imgSrc from note_info where nid=$nid limit 1"))[0];//旧图删除
                if($oldImg!=$imgSrc){
                    unlink($DISK_ROOT.$oldImg);
                    @unlink($DISK_ROOT.$oldImg.'.thumb');
                }
                maria($link,"update note_info set author='$author',imgSrc='$imgSrc',preview='$preview',catID=$catID,title='$title',type='$type',lut=now() where nid=$nid limit 1");
                maria($link,"update note_content set rawContent='$rawContent' where nid=$nid limit 1");
                //var_dump('second');
            }

            maria($link,"update note_info_tmp set author='$author',imgSrc='$imgSrc',preview='$preview',catID=$catID,inputTags='',title='$title',type='$type',lut=now(),asdraft=0 where nid=$nid limit 1");
            maria($link,"update note_content_tmp set rawContent='$rawContent'where nid=$nid limit 1");
            echo json_encode(['code'=>0]);
        }
    }
    else{
        //没有aid
        echo json_encode(['code'=>1]);
    }
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}



