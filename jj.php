<?php
require 'utils/ImgThumb.php';
require 'utils/utils.php';
//header('Access-Control-All-Origin:http://127.0.0.1:8080');
//header('Content-type:application/json');
//
//$a = ['aa','bb','cc'];
//$b = array('name'=>'jj','age'=>'66','options'=>array('o1','option2'));
//$c = $_GET;
//$d = $_POST;
//echo json_encode($b);
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
$files = scandir($DISK_ROOT.'/uploads/album');
var_dump($files);
foreach ($files as $each){
    thumb_img($DISK_ROOT.'/uploads/album/'.$each);
}
//thumb_img($DISK_ROOT.'/HI-1.jpg');
//thumb_img($DISK_ROOT.'/HI-2.jpg');
//thumb_img($DISK_ROOT.'/HI-3.jpg');
//thumb_img($DISK_ROOT.'/HI-4.jpg');
//thumb_img($DISK_ROOT.'/HI-5.jpg');
//thumb_img($DISK_ROOT.'/HI-6.jpg');
//thumb_img($DISK_ROOT.'/05.webp');















/*echo '<pre>';
$link = mysqli_connect('127.0.0.1:3306','root','237733');
mysqli_query($link,'set name utf8');
mysqli_query($link,'use test');
$res = mysqli_query($link,"select * from aa");
while($fres = mysqli_fetch_row($res))
var_dump($fres);

header('Content-type:text/html;charset=utf-8');
header('Content-type:application/octet-stream');
header('Accept-ranges:bytes');
header('Content-disposition:attachment/inline;filename=gg.txt');
header('Accept-length:' . filesize('gg.txt'));
echo file_get_contents('gg.txt');*/

/*foreach ($_FILES as $file){
    var_dump($file);
    var_dump(is_uploaded_file($file['tmp_name']));
    move_uploaded_file($file['tmp_name'],$file['name']);

}*/

/*$img = imagecreatefromjpeg('gg.jpg');
$tar = imagecreatetruecolor(100,100);
imagecopyresampled($tar,$img,0,0,0,0,100,100,imagesx($img),imagesy($img));
header('Content-type:image/png');
imagepng($tar);
imagedestroy($img);
print_r(getimagesize('gg.jpg'));*/