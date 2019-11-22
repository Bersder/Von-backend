<?php //设置页面接口
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
$key = 'DEEPDARKFANTASY1';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if (isset($_POST['encData'])&&isset($_POST['param'])){
        if($decrypted = openssl_decrypt(base64_decode($_POST['encData']),'aes-128-cbc',$key,OPENSSL_RAW_DATA,base64_decode($_POST['param']))){
            $data = json_decode($decrypted,true);
            //--------------------------------------↓标签的添加删除（需修改相关文章笔记）
            if (isset($data['newTag'])&&($newTag=maria_str_notnull_filter($data['newTag'],$link))){
                if($tagExist = mysqli_fetch_row(maria($link,"select 1 from Tag.tag_cloud where tagName=$newTag limit 1"))[0]?1:0)
                    echo json_encode(['code'=>0,'tagExist'=>$tagExist],JSON_NUMERIC_CHECK);
                else{
                    maria($link,"insert into Tag.tag_cloud (tid, tagName) values (null,$newTag)");
                    $id = mysqli_insert_id($link);
                    echo json_encode(['code'=>0,'newTagInfo'=>['id'=>$id,'tagName'=>$data['newTag'],'count'=>0],'tagExist'=>$tagExist],JSON_NUMERIC_CHECK);
                }
            }
            elseif (isset($data['delTagID'])&&($delTagID=positive_int_filter($data['delTagID']))){
                maria($link,"delete from Tag.tag_map where tid=$delTagID");
                maria($link,"delete from Tag.tag_map_tmp where tid=$delTagID");
                maria($link,"delete from Tag.tag_cloud where tid=$delTagID limit 1");
                echo json_encode(['code'=>0]);
            }
            //--------------------------------------↓系列的添加删除（需修改相关文章）
            elseif (isset($data['newSeries'])&&($newSeries=maria_str_notnull_filter($data['newSeries'],$link))&&($newSeriesDes=maria_str_notnull_filter($data['newSeriesDes'],$link))){
                if ($seriesExist = mysqli_fetch_row(maria($link,"select 1 from Article.series_link where seriesName=$newSeries limit 1"))[0]?1:0)
                    echo json_encode(['code'=>0,'seriesExist'=>$seriesExist],JSON_NUMERIC_CHECK);
                else{
                    maria($link,"insert into Article.series_link(sid,seriesName,description) values (null,$newSeries,$newSeriesDes)");
                    $id = mysqli_insert_id($link);
                    echo json_encode(['code'=>0,'id'=>$id,'seriesExist'=>$seriesExist],JSON_NUMERIC_CHECK);
                }
            }
            elseif (isset($data['delSeriesID'])&&($delSeriesID=positive_int_filter($data['delSeriesID']))){
                maria($link,"update Article.article_info set seriesID=null where seriesID=$delSeriesID");
                maria($link,"update Article.article_info_tmp set seriesID=null where seriesID=$delSeriesID");
                maria($link,"delete from Article.series_link where sid=$delSeriesID limit 1");
                echo json_encode(['code'=>0]);
            }
            //--------------------------------------↓类别的添加删除（需修改相关笔记）
            elseif (isset($data['newCatCN'])&&isset($data['newCatEN'])&&($newCatCN=maria_str_notnull_filter($data['newCatCN'],$link))&&($newCatEN=maria_str_notnull_filter($data['newCatEN'],$link))){
                if ($catExist = mysqli_fetch_row(maria($link,"select 1 from Note.note_category where catName=$newCatCN or catName_en=$newCatEN limit 1"))[0]?1:0)
                    echo json_encode(['code'=>0,'catExist'=>$catExist],JSON_NUMERIC_CHECK);
                else{
                    maria($link,"insert into Note.note_category (cid,catName_en,catName) values (null,$newCatEN,$newCatCN)");
                    $id = mysqli_insert_id($link);
                    echo json_encode(['code'=>0,'id'=>$id,'catExist'=>$catExist],JSON_NUMERIC_CHECK);
                }
            }
            elseif (isset($data['delCatID'])&&($delCatID=positive_int_filter($data['delCatID']))){
                maria($link,"update Note.note_info set catID=1 where catID=$delCatID");
                maria($link,"update Note.note_info_tmp set catID=1 where catID=$delCatID");
                maria($link,"delete from Note.note_category where cid=$delCatID limit 1");
                echo json_encode(['code'=>0]);
            }
            //--------------------------------------↓外部链接的添加删除
            elseif (isset($data['newLinkUrl'])&&isset($data['newLinkName'])&&isset($data['newLinkType'])){
                if (($newLinkUrl=maria_str_notnull_filter($data['newLinkUrl'],$link))&&($newLinkName=maria_str_notnull_filter($data['newLinkName'],$link))&&($newLinkType=maria_str_notnull_filter($data['newLinkType'],$link))){
                    if ($linkExist = mysqli_fetch_row(maria($link,"select 1 from Page.link where name=$newLinkName limit 1"))[0]?1:0)
                        echo json_encode(['code'=>0,'linkExist'=>$linkExist],JSON_NUMERIC_CHECK);
                    else{
                        maria($link,"insert into Page.link (id, type, name, url) values (null,$newLinkType,$newLinkName,$newLinkUrl)");
                        $id = mysqli_insert_id($link);
                        echo json_encode(['code'=>0,'id'=>$id,'linkExist'=>$linkExist],JSON_NUMERIC_CHECK);
                    }
                }
                else
                    echo json_encode(['code'=>1]);
            }
            elseif (isset($data['delLinkID'])&&($delLinkID=positive_int_filter($data['delLinkID']))){
                maria($link,"delete from Page.link where id=$delLinkID limit 1");
                echo json_encode(['code'=>0]);
            }
            //--------------------------------------↓页面背景的修改
            elseif (isset($data['headerID'])&&preg_match('/^\\d$/',$data['headerID'])){
                $headerID = $data['headerID'];
                $description = maria_escape($data['description'],$link);
                if ($data['file']&&($bgInfo=file_filter('bg',10000000))){
                    $oldBgInfo = mysqli_fetch_row(maria($link,"select imgSrc,type from Page.header_area where id=$headerID limit 1"));
                    $oldImgSrc = $oldBgInfo[0];$type = $oldBgInfo[1];
                    $path = '/site/header/'.$type.'_'.$bgInfo[2].$bgInfo[1];
                    move_uploaded_file($bgInfo[3],$DISK_ROOT.$path);//不管怎样先把图片弄过去，覆盖也没事
                    if ($path!=$oldImgSrc)unlink($DISK_ROOT.$oldImgSrc);//当原图片类型不一致时删除旧的
                    $path = maria_escape($path,$link);
                    maria($link,"update Page.header_area set imgSrc=$path,description=$description where id=$headerID limit 1");
                }
                else
                    maria($link,"update Page.header_area set description=$description where id=$headerID limit 1");
                echo json_encode(['code'=>0,'data'=>['des'=>$data['description']]]);
            }
        }
        else
            echo json_encode(['code'=>1]);

    }
    else{
        $tagCountList = [];
        $res = maria($link,"
        select tid as id,tagName,ifnull(count,0) as count
        from Tag.tag_cloud as tc left join (select count(tid) as count,tid from Tag.tag_map group by tid) as tmp
        using(tid)
        order by tagName asc;
        ");
        while ($each = mysqli_fetch_assoc($res))$tagCountList[] = $each;


        $seriesList = [];//系列关联文章计数
        $res = maria($link,"
        select sid as id,seriesName as name,count
        from (select seriesID,count(*) as count from Article.article_info where seriesID is not null group by seriesID) as tmp left join Article.series_link as sl 
        on tmp.seriesID=sl.sid
        union 
        select sid as id,seriesName as name,0 as count
        from Article.series_link
        where sid not in (select distinct seriesID from Article.article_info where seriesID is not null)
        ");
        while ($each = mysqli_fetch_assoc($res))$seriesList[] = $each;
        array_multisort(array_column($seriesList,'name'),SORT_ASC,$seriesList);//按系列名升序排序

        $categoryList = [];
        $res = maria($link,"
        select cid as id,catName as nameCN,catName_en as nameEN,count
        from (select catID,count(*) as count from Note.note_info where catID>1 group by catID) as tmp left join Note.note_category as nc
        on tmp.catID=nc.cid
        union 
        select cid as id,catName as nameCN,catName_en as nameEN,0 as count
        from Note.note_category
        where cid>1 and cid not in (select distinct catID from Note.note_info)
        ");
        while ($each = mysqli_fetch_assoc($res))$categoryList[] = $each;
        array_multisort(array_column($categoryList,'nameCN'),SORT_ASC,$categoryList);//按分类名升序排序

        $headers = [];
        $res = maria($link,"select * from Page.header_area order by id asc");
        while ($each = mysqli_fetch_assoc($res))$headers[] = $each;
        $outerLinks = [];
        $res = maria($link,"select * from Page.link order by name asc ");
        while ($each = mysqli_fetch_assoc($res)){
            if (isset($outerLinks[$each['type']]))
                $outerLinks[$each['type']][] = $each;
            else{
                $outerLinks[$each['type']] = [];
                $outerLinks[$each['type']][] = $each;
            }
        }
        echo json_encode(['code'=>0,'data'=>['tagCountList'=>$tagCountList,'seriesList'=>$seriesList,'categoryList'=>$categoryList,'headers'=>$headers,'outerLinks'=>$outerLinks]],JSON_NUMERIC_CHECK);
    }
} else {
    http_response_code(401);
    echo json_encode(['code' => 1]);
}