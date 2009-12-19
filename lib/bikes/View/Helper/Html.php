<?php

/**
 *
 * @author Kyle Terry
 * @class bikes_View_Helper_Html
 */
class bikes_View_Helper_Html extends bikes_View_Helper_Abstract{

	/**
	 *
	 * @var array $docTypes
	 */
	protected $docTypes = array(
		'html4-strict'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
		'html4-trans'  	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
		'html4-frame'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		'xhtml-strict' 	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		'xhtml-trans' 	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		'xhtml-frame' 	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
		'xhtml11' 	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
	);

	/**
	 *
	 * @return string
	 */
	public function docType($type){
		if(isset($this->docTypes[$type])){
			return $this->docTypes[$type] . "\n";
		}
		return false;
	}

	/**
	 *
	 * @return string $tag
	 */
	public function setCSS($url, $file){
		$path = $url . DIRECTORY_SEPARATOR . $file;
		$tag = '<link rel="stylesheet" type="text/css" href="' . $path . '" />';
		return $tag . "\n";
	}

	/**
	 *
	 * @return string $tag
	 */
	public function setJavascriptFile($url, $file){
		$path = $url . DIRECTORY_SEPARATOR . $file;
		$tag = '<script type="text/javascript" src="' . $path . '"></script>';
		return $tag . "\n";
	}

	/**
	 *
	 * @return string $tag
	 */
	public function setFavicon($url, $file){
		$path = $url . DIRECTORY_SEPARATOR . $file;
		$tag = '<link rel="shortcut icon" href="' . $path . '" type="image/x-icon" />';
		return $tag . "\n";
	}

	/**
	 *
	 * @return string $tag
	 */
	public function setLink($url, $linkText, $otherJunk = null, $newPage = false){
		$tag = '<a href="' . $url . '" ';
		if(true === $newPage){
			$tag .= 'target="_blank"';
		}
		if(isset($otherJunk)){
			$tag .= ' ' . $otherJunk . ' ';
		}
		$tag .= '>' . $linkText . '</a>';
		return $tag;
	}
}
