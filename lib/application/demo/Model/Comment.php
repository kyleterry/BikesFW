<?php

class demo_Model_Comment extends bikes_Model_Abstract{
	protected $table = 'comment';
	protected $name = 'comment';
	protected $columns = array(
		'id'		=> array('required' => false),
		'body'		=> array('required' => true),
		'postId'	=> array('required' => true),
		'userId'	=> array('required' => true),
		'deleted'	=> array('required' => false),
		'postDate'	=> array('required' => false),
	);
}
