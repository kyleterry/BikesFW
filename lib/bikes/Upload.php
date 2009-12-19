<?php
class bikes_Upload {


    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * Process an uploaded file
     *
     * @param bikes_upload_HandlerAbstract $handler
     * @return bikes_upload_File
     * @throws Exception
     */
    public function process(bikes_Upload_HandlerAbstract $handler) {
        $file = $this->createUploadFile($handler);
        $handler->preProcess($file);
        $handler->process($file);
        return $file;
    }

    /**
     * Is there an upload for this handler?
     *
     * @param $handler
     * @return bool
     * @throws Exception
     */
    public function has(bikes_Upload_HandlerAbstract $handler) {
        $k = $handler->getField();

        if (!isset($_FILES[$k]) ||
            0 == $_FILES[$k]['size'] ||
            UPLOAD_ERR_NO_FILE == $_FILES[$k]['error']
        ) {
            return false;
        }

        return true;
    }


    /**
     * @param bikes_upload_HandlerAbstract $handler
     * @return bikes_upload_File
     * @throws Exception
     */
    private function createUploadFile(bikes_Upload_HandlerAbstract $handler) {
        $file = new bikes_Upload_File;
        $k    = $handler->getField();

	if(!$handler->isName()){
		$file->name = $handler->getName();
        	$file->rawName  = $handler->getName();
	} else {
	        $file->name     = $_FILES[$k]['name'];
        	$file->rawName  = $_FILES[$k]['name'];
	}
        $file->rawName  = $_FILES[$k]['name'];
	$file->extension= strstr($_FILES[$k]['name'], '.');
	$file->name    .= $file->extension;
        $file->rawType  = $_FILES[$k]['type'];
        $file->size     = $_FILES[$k]['size'];
        $file->tempName = $_FILES[$k]['tmp_name'];
        $file->error    = $_FILES[$k]['error'];

        if (UPLOAD_ERR_OK !== $_FILES[$k]['error']) {
            switch ($_FILES[$k]['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    throw new Exception('The file you uploaded exceeds the maximum size of '. number_format(self::maxUploadSize(), 0, '', ',') .'.');
                    break;

                default:
                    throw new Exception("There was error uploading the file.");
                    break;
            }
        }

        $this->validateMime($handler, $file);

        return $file;
    }

    /**
     * Checks the handlers mime types against what was uploaded.
     *
     * @param bikes_upload_HandlerAbstract $handler
     * @param bikes_upload_File $file
     * @return void
     * @throws Exception
     */
    private function validateMime(bikes_Upload_HandlerAbstract $handler, bikes_upload_File $file) {
        $mimes = $handler->getMimes();
        if (! function_exists('mime_content_type')) {
            trigger_error("Unable to validate your file mime type.");
            throw new Exception("Unable to validate your file mime type.");
        }

        $mime = mime_content_type($file->tempName);
        if (!in_array($mime, $mimes)) {
            throw new Exception("The mime type ". $mime ." is invalid for file ". $file->rawName .".");
        }

        $file->type = $mime;
    }


    /**
     * Static method to return valid image mime types
     *
     * @return array
     */
    public static function getImageMimes() {
        return array('image/jpeg', 'image/jpeg', 'image/png', 'image/gif');
    }


    /**
     * Max upload filesize
     *
     * @return int
     */
    public static function maxUploadSize() {
        $filesize = ini_get('upload_max_filesize');

        if ($postsize = ini_get('post_max_size')) {
            return min(self::realSize($filesize), self::realSize($postsize));
        } else {
            return self::realSize($filesize);
        }
    }


    /**
     * Converts numbers like 10M into bikess
     * Stolen from phpMyAdmin which stole from Moodle (http://moodle.org) by
     * Martin Dougiamas
     *
     * @param   string  $size
     * @return  integer $size
     */
    public static function realSize($size = 0) {
        if (!$size) {
            return 0;
        }
        $scan['MB'] = 1048576;
        $scan['Mb'] = 1048576;
        $scan['M']  = 1048576;
        $scan['m']  = 1048576;
        $scan['KB'] =    1024;
        $scan['Kb'] =    1024;
        $scan['K']  =    1024;
        $scan['k']  =    1024;

        while (list($key) = each($scan)) {
            if ((strlen($size) > strlen($key))
              && (substr($size, strlen($size) - strlen($key)) == $key)) {
                $size = substr($size, 0, strlen($size) - strlen($key)) * $scan[$key];
                break;
            }
        }
        return $size;
    }
}
