<?php

/**
 * stolen from eric. 
 * 
 * @package 
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com 
 * @license BSD (Inlcuded)
 */
class bikes_upload_File {

	/**
	 * Name of the uploaded file.  This may vary from the rawName since it gets
	 * filtered.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Raw name provided by the client
	 *
	 * @var string
	 */
	public $rawName;

	/**
	 * Actual mimetype determined using Fileinfo or mime_content_type
	 *
	 * @var string
	 */
	public $type;
	
	/**
	 * extension 
	 * 
	 * @var mixed
	 * @access public
	 */
	public $extension;

	/**
	 * The raw mime type provided by the client
	 *
	 * @var string
	 */
	public $rawType;

	/**
	 * Filesize
	 *
	 * @var int
	 */
	public $size;

	/**
	 * Location of file on disc
	 *
	 * @var string
	 */
	public $tempName;

}
