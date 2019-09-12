<?php


class ImgThumb{
    private $type;
    private $width;
    private $height;
    private $maxSize;
    private $resize_width;
    private $resize_height;
    private $src;
    private $dst;
    private $tmpImg;

    function __construct($src,$dst,$maxSize=300){
        $this->src = $src;
        $this->dst = $dst;
        $this->maxSize = $maxSize;
        $this->init_img();
        $this->thumb_img();
    }


    private function init_img(){
        $this->type = strtolower(substr(strrchr($this->src,"."),1));
        if ($this->type=='jpg'||$this->type=='jpeg')
            $this->tmpImg = imagecreatefromjpeg($this->src);
        elseif($this->type=='png')
            $this->tmpImg = imagecreatefrompng($this->src);
        elseif($this->type=='gif')
            $this->tmpImg = imagecreatefromgif($this->src);
//        var_dump($this->tmpImg);
        $this->width = imagesx($this->tmpImg);
        $this->height = imagesy($this->tmpImg);
    }

    private function thumb_img(){
        if ($this->height>$this->maxSize||$this->width>$this->maxSize){
            if ($this->width>$this->height){
                $this->resize_width = $this->maxSize;
                $this->resize_height = $this->resize_width/$this->width*$this->height;
            }
            elseif($this->width<$this->height){
                $this->resize_height = $this->maxSize;
                $this->resize_width = $this->resize_height/$this->height*$this->width;
            }
            else{
                $this->resize_height = $this->resize_height = $this->maxSize;
            }
        }
        else
            $this->resize_height = $this->resize_width = $this->maxSize;

        $thumbImg = imagecreatetruecolor($this->resize_width,$this->resize_height);
        if ($this->type=='png')
            imagefill($thumbImg,0,0,imagecolorallocatealpha($thumbImg,0,0,0,127));

        imagecopyresampled($thumbImg,$this->tmpImg,0,0,0,0,$this->resize_width,$this->resize_height,$this->width,$this->height);





        if ($this->type=='png'){
            imagesavealpha($thumbImg,true);
            imagepng($thumbImg,$this->dst);
        }
        else
            imagejpeg($thumbImg,$this->dst,20);
    }
}