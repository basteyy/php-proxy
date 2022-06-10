<?php

namespace Proxy\Event;

class ProxyEvent implements \ArrayAccess {
	private mixed $data;
	
	public function __construct($data = array()){
		$this->data = $data;
	}
	
	public function offsetSet($offset, $value): void {
		
		if(is_null($offset)) {
			$this->data[] = $value;
		} else {
			$this->data[$offset] = $value;
		}
	}
	
	public function offsetExists($offset): bool
    {
		return isset($this->data[$offset]);
	}

	public function offsetUnset($offset): void{
		unset($this->data[$offset]);
	}

	public function offsetGet($offset): mixed{
		return $this->data[$offset] ?? null;
	}
	
}
