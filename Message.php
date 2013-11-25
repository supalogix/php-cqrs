<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (C) 2013 Jonathan Nacionales
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

abstract class Message {
	private $properties = array();

	final public function getArray() {
		return $this->properties;
	}

	final public function __construct( array $args = null ) {
		$properties = static::getProperties();

		if( $args == null )
			return;

		foreach( $args as $key => $value ) {
			$this->set( $key, $value );
		}
	}

	final public function __call( $functionName, $args ) {
		if( strpos( $functionName, "get" ) === 0 ) {
			$variableName = lcfirst( substr( $functionName, strlen("get") ) );
			$variableValue = $this->get( $variableName );
			return $variableValue;
		}

		if( strpos( $functionName, "set" ) === 0 ) {
			$variableName = lcfirst( substr( $functionName, strlen("set") ) );
			return $this->set( $variableName, $args[0] );
		}

		throw new \Exception( 
			sprintf("Tried to call unknown method %s::%s",
				get_class($this),
				$functionName
			)
		);
	}

	final public function __get( $key ) {
		$properties = static::getProperties();

		return $this->get( $key );
	}

	final public function __set( $key, $value ) {
		return $this->set( $key, $value );
	}

	final protected function get( $key ) {
		if( !array_key_exists( $key, $this->properties ) )
			return null;

		return $this->properties[ $key ];
	}

	final protected function set( $key, $value ) {

		$errors = $this->getSetterExceptions( $key, $value );

		if( !empty($errors) )
			throw $errors[0];

		$this->properties[$key] = $value;

		return $this;
	}

	final protected function getSetterExceptions( $key, $value ) {
		$properties = static::getProperties();

		$errors = array();

		if( !array_key_exists( $key, $properties ) )
			$errors[] = new \InvalidArgumentException(
				sprintf( "'%s' is not a valid property for %s",
					$key,
					get_class( $this )
				)
			);

		$property = (object)$properties[$key];

		$type = $this->getType( $value );

		if( $property->type !== $type ) 
			$errors[] = new \InvalidArgumentException(
				sprintf( "'%s' is of type '%s' but this function expected '%s' for class '%s'",
					$key,
					$type,
					$property->type,
					get_class( $this )
				)
			);

		return $errors;
	}

	final public static function getInstance() {
		return new static();
	}

	final protected function getType( $value ) {
		$property = gettype( $value );

		if( $property === "object" )
			return get_class( $value );
		
		return $property;
	}

	abstract public static function getProperties();
}

?>
