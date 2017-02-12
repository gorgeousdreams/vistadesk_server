<?php
class ImageModel extends Eloquent {
    
    public $timestamps = false;
    protected $table = 'images';
    
    public static $thumbWidth = 300;
    public static $thumbHeight = 300;
    
           
    public function file() {
        return $this->belongsTo('FileModel','file_id');
    }
    
    
    private function deleteImageThumb($filename,$new_width,$new_height) {
        $file_path_parts = pathinfo($filename);
        $deleted_filename = $file_path_parts['dirname']."/".$file_path_parts['filename'].'_'.$new_width."x".$new_height.".".$file_path_parts['extension'];
        if (file_exists($deleted_filename)) {
            unlink($deleted_filename);
        }
    }
    
    private function createImageThumb($filename,$new_width,$new_height,$quality) {
                //create thumbnail and save as $filename.'_'.$new_width."x".$new_height
        $image_p = imagecreatetruecolor($new_width, $new_height);
        $image_orig = imagecreatefromjpeg($filename);
        $imageSize = getimagesize($filename);
        imagecopyresampled($image_p, $image_orig, 0, 0, 0, 0, $new_width, $new_height, $imageSize[0], $imageSize[1]);
        $file_path_parts = pathinfo($filename);
        imagejpeg($image_p, $file_path_parts['dirname']."/".$file_path_parts['filename'].'_'.$new_width."x".$new_height.".".$file_path_parts['extension'], 80);
        imagedestroy($image_p);
        imagedestroy($image_orig);
        //dd($file_path_parts['dirname'].$file_path_parts['filename'].'_'.$new_width."x".$new_height.".".$file_path_parts['extension']);
    }
    
    public function createNewImage($uploadFile,$uploadFilePath) {
        $uploadFileName = str_random(16).'_userpic.'.$uploadFile->getClientOriginalExtension();
        $uploadFile->move($_SERVER['DOCUMENT_ROOT'].$uploadFilePath, $uploadFileName);
        self::createImageThumb($_SERVER['DOCUMENT_ROOT'].$uploadFilePath.$uploadFileName,self::$thumbWidth,self::$thumbHeight,80);
        
        $file = new \FileModel;
        $file->filename = $uploadFileName;
        $file->path = $uploadFilePath;
        $file->save();
        $this->file_id = $file->id;
        $imageSize = getimagesize($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename);
        $this->width = $imageSize[0];
        $this->height = $imageSize[1];
        $this->save();
        return $this;
    }
    
    public function updateImage($id,$uploadFile,$uploadFilePath) {
        $image = ImageModel::find($id);
        $file = $image->file()->first();
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename)) {
            unlink($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename);
        }
        self::deleteImageThumb($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename,self::$thumbWidth,self::$thumbHeight);
        $uploadFileName = str_random(16).'_userpic.'.$uploadFile->getClientOriginalExtension();
        $uploadFile->move($_SERVER['DOCUMENT_ROOT'].$uploadFilePath, $uploadFileName);
        self::createImageThumb($_SERVER['DOCUMENT_ROOT'].$uploadFilePath.$uploadFileName,self::$thumbWidth,self::$thumbHeight,80);
        $file->filename = $uploadFileName;
        $file->path = $uploadFilePath;
        $file->save();
        $image->file_id = $file->id;
        $imageSize = getimagesize($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename);
        $image->width = $imageSize[0];
        $image->height = $imageSize[1];
        $image->save();
        return $image;
    }
    
    public function deleteImage($id) {
        $image = ImageModel::find($id);
        $file = $image->file()->first();
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename)) {
            unlink($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename);
        }
        self::deleteImageThumb($_SERVER['DOCUMENT_ROOT'].$file->path.$file->filename,self::$thumbWidth,self::$thumbHeight);
        $image->delete();
        $file->delete();
        return true;
    }
    
    public function imageLink() {
        $file = $this->file()->first();
        return $file->path.$file->filename;
    }
    
    public function imageLinkThumb($new_width,$new_height) {
        $file = $this->file()->first();
        $file_path_parts = pathinfo($file->path.$file->filename);
        return $file_path_parts['dirname']."/".$file_path_parts['filename'].'_'.$new_width."x".$new_height.".".$file_path_parts['extension'];
    }
    
}