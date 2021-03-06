<?php
abstract class bikes_upload_HandlerAbstract {

    protected $field;
    protected $name = null;
    protected $mimes = array();
    protected $url;
    protected $path;

    /**
     * Returns an array of mime types allowed by this handler
     *
     * @return array
     */
    public function getMimes() {
        if (0 === count($this->mimes)) {
            throw new Exception("Upload has no defined mime types.");
        }
        return $this->mimes;
    }

    /**
     * Set an array of mime types allowed
     *
     * @param array $mimes
     */
    public function setMimes(array $mimes) {
        $this->mimes = $mimes;
    }

    public function isName(){
	if(!empty($this->name)){
		return true;
	}
	return false;
    }

    public function getName(){
	return $this->name;
    }

    public function setName($name){
	$this->name = $name;
    }

    /**
     * Url to this file on the server
     *
     * @return string
     */
    public function getUrl() {
        if (null === $this->url) {
            throw new Exception("Upload url is not defined.");
        }
        return $this->url;
    }
    
    public function getFullUrl(){
		return $this->getUrl() . $this->name . $this->file->extension;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * Filesystem path to file on server
     *
     * @return string
     */
    public function getPath() {
        if (null === $this->path) {
            throw new Exception("Upload path is not defined.");
        }
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * The $_FILES key name
     *
     * @return string
     */
    public function getField() {
        if (null === $this->field) {
            throw new Exception("Upload field is not defined.");
        }
        return $this->field;
    }

    /**
     * The $_FILES key name
     *
     * @param string $field
     */
    public function setField($field) {
        $this->field = $field;
    }

    /**
     * @throws Exception
     */
    public function preProcess(bikes_upload_File $file) {
        $path = $this->getPath();
        if (!is_dir($path)) {
            if (mkdir($path, 0755, true) !== true) {
                trigger_error("Unable to create the upload path ({$path})");
                throw new Exception('Invalid upload path.');
            }
        }

        /*$original = bikes_File::sanitizeFilename($file->name);
        $original = bikes_File::truncateFilename($original, 32);

        $out        = bikes_File::generateUniqueFilename($path, $original);
        $file->name = basename($out);*/
    }

    /**
     * Override this method in your child class to add extra functionality.  For
     * example in an image upload you could create an instance of bikes_Image &
     * apply filters in your custom process method.  Then call
     * parent::process($file) when you're done.
     *
     * @param bikes_upload_File $file
     * @return void
     * @throws Exception
     */
    public function process(bikes_upload_File $file) {
    	$this->file = $file;
        $this->moveUploadedFile($file);
    }

    /**
     * Delete a file off the server that belongs to this handler
     *
     * @param string $filename
     * @return void
     * @throws Exception
     */
    public function remove($filename) {
        $path = $this->getPath() . basename($filename);
        if (is_file($path) && true !== unlink($path)) {
            trigger_error("Unable to remove uploaded file: unlink({$path})");
            throw new Exception("Unable to remove {$filename}");
        }
    }

    /**
     * Moves the uploaded file from the temp location to the destination path
     * defined by getPath() & the cleaned filename.
     *
     * @param bikes_upload_File $file
     * @return void
     */
    protected function moveUploadedFile(bikes_upload_File $file) {
	if($this->isName()){
		$dst = $this->getPath() . $this->name . $file->extension;
	} else {
	        $dst = $this->getPath() . $file->name;
	}
        if (true !== move_uploaded_file($file->tempName, $dst)) {
            trigger_error(sprintf("Unable to move_uploaded_file('%s', '%s')",
                $file->tempName,
                $dst
            ));
            throw new Exception('We were unable to move the uploaded file '. $file->name .' into the upload folder.');
        }
    }

    /**
     * Returns the destination path for the uploaded file
     *
     * @param bikes_upload_File $file
     * @return string
     */
    public function getDestination(bikes_upload_File $file) {
        return $this->getPath() . $file->name;
    }

}
