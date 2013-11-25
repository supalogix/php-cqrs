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

require_once( "HandlerRepository.php" );

class MessageBus {
	public static function getHandlers() {
		return HandlerRepository::getHandlers();	
	}

	public static function send( $message ) {
		$errors = static::validate( $message );
		if( !empty($errors) )
			throw $errors[0];

		$handlers = HandlerRepository::getHandlers();

		$eventName = get_class( $message );

		if( !empty( $handlers[ $eventName ] ) ) {
			foreach( $handlers[ $eventName ] as $className ) {
				$functionName = "handle" . $eventName;
				$class = new $className();
				$class->$functionName( $message );
			}
		}
	}

	protected static function validate( $message ) {
		$properties = $message->getProperties();

		$errors = array();

		foreach( $properties as $property => $meta ) {
			$value = $message->$property;

			if( $meta["required"] === true && $value == null )
				$errors[] = new InvalidArgumentException( sprintf(
					"'%s' is a required property of %s, but '%s' is null",
					$property,
					get_class($message),
					$property
				));
				
		}

		return $errors;
	}
}

 ?>
