<?php

abstract class bikes_Cache_Abstract{
	public function __construct(){
	}

	abstract public function connect();

	abstract public function add($index, $value, $ttl = 1500);

	abstract public function get($index);

	abstract public function flush();
}
