<?php //空间主页接口
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    $artPV = maria_scalar($link,"select sum(readCount) from Article.article_info");
    $notePV = maria_scalar($link,"select sum(readCount) from Note.note_info");
    $artDiffPV = maria_scalar($link,"select count(*) from Tmp.visitor_log where visitTime between date_sub(curdate(),interval 1 day) and curdate() and xtype<>'note'");
    $noteDiffPV = maria_scalar($link,"select count(*) from Tmp.visitor_log where visitTime between date_sub(curdate(),interval 1 day) and curdate() and xtype='note'");
    $commentNum = maria_scalar($link,"select count(*) from Comment.comment");
    $commentDiffNum = maria_scalar($link,"select count(*) from Comment.comment where datetime between date_sub(curdate(),interval 1 day) and curdate()");
    $statisticsData = [
        ['des'=>'文章阅读','sum'=>$artPV,'diff'=>$artDiffPV],
        ['des'=>'笔记阅读','sum'=>$notePV,'diff'=>$noteDiffPV],
        ['des'=>'评论数量','sum'=>$commentNum,'diff'=>$commentDiffNum],
    ];
    echo json_encode(['code'=>0,'data'=>[
        'statisticsData'=>$statisticsData,

    ]],JSON_NUMERIC_CHECK);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}
