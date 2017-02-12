<?php
class FileModel extends Eloquent {
    protected $table = 'files';
    /**
     * 
     * @param file $uploadFile file 
     * @param sting $namePrefix prefix to file name
     * @param string $uploadFolder  Folder in which upload file
     * @return object File model
    */
    public function createNewFile($uploadFile, $uploadFolder, $fileNamePrefix = null, $fileName = null) {
        if ($fileName) {
            $uploadFileName = $fileName.".".$uploadFile->getClientOriginalExtension();  
        } else {
            $uploadFileName = str_random(16).$namePrefix.".".$uploadFile->getClientOriginalExtension();            
        }
        $uploadFilePath = $_SERVER['DOCUMENT_ROOT'].$uploadFolder;
        $uploadFile->move($uploadFilePath, $uploadFileName);
        $this->filename = $uploadFileName;
        $this->path = $uploadFilePath;
        $this->save();
        return $this;
    }
    
        /**
     * @param integer $id fileID
     * @param file $uploadFile file 
     * @param sting $namePrefix prefix to file name
     * @param string $uploadFolder  Folder in which upload file
     * @return object File model
    */
    public function updateFile($id, $uploadFile, $uploadFolder, $fileNamePrefix = null, $fileName = null) {
        $file = FileModel::find($id);
        if (file_exists($file->path.$file->filename)) {
            unlink($file->path.$file->filename);
        }
        if ($fileName) {
            $uploadFileName = $fileName.".".$uploadFile->getClientOriginalExtension();  
        } else {
            $uploadFileName = str_random(16).$namePrefix.".".$uploadFile->getClientOriginalExtension();            
        }
        $uploadFilePath = $_SERVER['DOCUMENT_ROOT'].$uploadFolder;
        $uploadFile->move($uploadFilePath, $uploadFileName);
        $file->filename = $uploadFileName;
        $file->path = $uploadFilePath;
        $file->save();
        return $file;
    }
    
    
}