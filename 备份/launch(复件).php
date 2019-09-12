<?php
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
function maria($link,$sql){
    $res = mysqli_query($link,$sql);
    if(!$res){
        echo '错误编号：'.mysqli_errno($link).'<br>';
        echo '错误信息：'.mysqli_error($link).'<br>';
        exit;
    }
    return $res;
}
function add_newTags($link){
    if(isset($_POST['newTags'])&&$_POST['newTags']){//有新标签时，添加至标签云并与tags拼接
        $newTags = $_POST['newTags'];
        foreach (explode(',',$newTags) as $value){
            if(!mysqli_fetch_row(maria($link,"select 1 from Tag.tag_cloud where tagName='$value' limit 1"))[0])
            maria($link,"insert into Tag.tag_cloud values(null,'$value',',',',')");
        }
        return $_POST['tags']?$_POST['tags'].','.$newTags:$newTags;
    }
    else return $_POST['tags'];
}

if(isset($_GET['aid'])&&in_array($_POST['type'],['anime','code','game','trivial'])){
    maria($link,'use Article');
    $aid = $_GET['aid'];
    if(preg_match('/^[1-9]\\d*$/',$aid)){
        $type = $_POST['type'];
        $aid2 = $aid.',';

        $isFirst = !(mysqli_fetch_row(maria($link,"select 1 from article_info where aid=$aid limit 1"))[0]); //可能修改
        $imgSrc = $_POST['imgSrc'];
        $author = $_POST['author'];
        $preview = $_POST['preview'];
        $rawContent = $_POST['rawContent'];
        $series = $_POST['series']?"'".$_POST['series']."'":'null';
        $title = $_POST['title'];

        $tags = add_newTags($link);
        foreach (explode(',',$tags) as $value){//把aid纳入各种tag下,删除的等下面
            if(!preg_match('/,'.$aid.',/',mysqli_fetch_row(maria($link,"select relateArt from Tag.tag_cloud where tagName='$value' limit 1"))[0]))
            maria($link,"update Tag.tag_cloud set relateArt=concat(relateArt,'$aid2') where tagName='$value' limit 1");
        }



        //然后录入文章信息
        if($isFirst){ //首次发布，往非tmp中插入新行、设置草稿备份,关联series，
            maria($link,"insert into article_info  (aid, type, title, preview, imgSrc, author, time, lut,tags, series) values($aid,'$type','$title','$preview','$imgSrc','$author',now(),now(),'$tags',$series)");
            maria($link,"insert into article_content (aid, rawContent) values($aid,'$rawContent')");
            if ($series!='null')maria($link,"update Series.series_link set relateArt=concat(relateArt,'$aid2') where seriesName=$series limit 1");
            maria($link,"update article_info_tmp set asbu=1,time=now() where aid=$aid limit 1");
            var_dump('first');
        }
       else{//二次更改发布
           $oldTarget = ','.$aid.',';
           $oldTags = explode(',',mysqli_fetch_row(maria($link,"select tags from article_info where aid=$aid limit 1"))[0]);
           $deleteTags = array_diff($oldTags,explode(',',$tags));
           foreach ($deleteTags as $value){ //标签删除，和数据库比较取得被删除的标签，然后删除对应标签下的id
//               $ad =  preg_replace('/,'.$aid.',/',',',mysqli_fetch_row(maria($link,"select relateArt from Tag.tag_cloud where tagName='$value' limit 1"))[0]);
//               maria($link,"update Tag.tag_cloud set relateArt='$ad' where tagName='$value' limit 1");
               maria($link,"update Tag.tag_cloud set relateArt=replace(relateArt,'$oldTarget',',') where tagName='$value' limit 1");
           }
           $oldSeries = mysqli_fetch_row(maria($link,"select series from article_info where aid=$aid limit 1"))[0];
           $oldSeries = $oldSeries?"'".$oldSeries."'":'null';
           if ($oldSeries!=$series){
               if ($oldSeries!='null'){
                   maria($link,"update Series.series_link set relateArt=replace(relateArt,'$oldTarget',',') where seriesName=$oldSeries limit 1");
               }
               if ($series!='null'){
                   maria($link,"update Series.series_link set relateArt=concat(relateArt,'$aid2') where seriesName=$series limit 1");
               }
           }
           $oldImg = mysqli_fetch_row(maria($link,"select imgSrc from article_info where aid=$aid limit 1"))[0];//旧图删除
           if($oldImg!=$imgSrc)unlink($DISK_ROOT.$oldImg);//避免同名不同格式
           maria($link,"update article_info set author='$author',imgSrc='$imgSrc',preview='$preview',series=$series,tags='$tags',title='$title',type='$type',lut=now() where aid=$aid limit 1");
           maria($link,"update article_content set rawContent='$rawContent' where aid=$aid limit 1");
           var_dump('second');
       }
        //草稿跟随发布更新，并取消草稿
        maria($link,"update article_info_tmp set author='$author',imgSrc='$imgSrc',preview='$preview',series=$series,tags='$tags',inputTags='',title='$title',type='$type',lut=now(),asdraft=0 where aid=$aid limit 1");
        maria($link,"update article_content_tmp set rawContent='$rawContent'where aid=$aid limit 1");
    }
}
elseif(isset($_GET['nid'])&&$_POST['type']=='note'){
    maria($link,'use Note');
    $nid = $_GET['nid'];
    if(preg_match('/^[1-9]\\d*$/',$nid)){
        $type = 'note';
        $nid2 = $nid.',';
        $isFirst = !(mysqli_fetch_row(maria($link,"select 1 from note_info where nid=$nid limit 1"))[0]);
        $imgSrc = $_POST['imgSrc'];
        $author = $_POST['author'];
        $preview = $_POST['preview'];
        $rawContent = $_POST['rawContent'];
        $category = $_POST['category'];
        $title = $_POST['title'];

        $tags = add_newTags($link);
        foreach (explode(',',$tags) as $value){
            if(!preg_match('/,'.$nid.',/',mysqli_fetch_row(maria($link,"select relateNote from Tag.tag_cloud where tagName='$value' limit 1"))[0]))
            maria($link,"update Tag.tag_cloud set relateNote=concat(relateNote,'$nid2') where tagName='$value' limit 1");
        }

        if($isFirst){
            maria($link,"insert into note_info (nid, type, title, preview, imgSrc, author, time, lut, tags, category) values ($nid,'note','$title','$preview','$imgSrc','$author',now(),now(),'$tags','$category')");
            maria($link,"insert into note_content (nid, rawContent) values ($nid,'$rawContent')");
            maria($link,"update note_category set relateNote=concat(relateNote,'$nid2') where catName_en='$category' limit 1");
            maria($link,"update note_info_tmp set asbu=1,time=now() where nid=$nid limit 1");
            var_dump('first');
        }
        else{
            $oldTarget = ','.$nid.',';
            $oldTags = explode(',',mysqli_fetch_row(maria($link,"select tags from note_info where nid=$nid limit 1"))[0]);
            $deleteTags = array_diff($oldTags,explode(',',$tags));
            foreach ($deleteTags as $value){ //标签删除，和数据库比较取得被删除的标签，然后删除对应标签下的id
//                $ad =  preg_replace('/,'.$nid.',/',',',mysqli_fetch_row(maria($link,"select relateNote from Tag.tag_cloud where tagName='$value' limit 1"))[0]);
//                maria($link,"update Tag.tag_cloud set relateNote='$ad' where tagName='$value' limit 1");
                maria($link,"update Tag.tag_cloud set relateNote=replace(relateNote,'$oldTarget',',') where tagName='$value' limit 1");
            }
            $oldCat = mysqli_fetch_row(maria($link,"select category from note_info where nid=$nid limit 1"))[0];
            if ($oldCat != $category) {
                maria($link,"update note_category set relateNote=replace(relateNote,'$oldTarget',',') where catName_en='$oldCat' limit 1");
                maria($link,"update note_category set relateNote=concat(relateNote,'$nid2') where catName_en='$category' limit 1");
            }
            $oldImg = mysqli_fetch_row(maria($link,"select imgSrc from note_info where nid=$nid limit 1"))[0];//旧图删除
            if($oldImg!=$imgSrc)unlink($DISK_ROOT.$oldImg);
            maria($link,"update note_info set author='$author',imgSrc='$imgSrc',preview='$preview',category='$category',tags='$tags',title='$title',type='$type',lut=now() where nid=$nid limit 1");
            maria($link,"update note_content set rawContent='$rawContent' where nid=$nid limit 1");
            var_dump('second');
        }

        maria($link,"update note_info_tmp set author='$author',imgSrc='$imgSrc',preview='$preview',category='$category',tags='$tags',inputTags='',title='$title',type='$type',lut=now(),asdraft=0 where nid=$nid limit 1");
        maria($link,"update note_content_tmp set rawContent='$rawContent'where nid=$nid limit 1");
    }
}
else{
    //没有aid
    echo json_encode(['error'=>1]);
}
