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

class HandlerRepository {
   public static function getHandlers() {
      $classes =  get_declared_classes();
      $handlers = array();
      foreach( $classes as $className ) {
         $rc = new ReflectionClass($className);
         $interfaces = $rc->getInterfaceNames();

			if( in_array( "CommandHandler", $interfaces ) || 
				in_array( "EventHandler", $interfaces ) ) {
				$methods = $rc->getMethods();

				foreach( $methods as $method ) {
					$name = $method->name;
					$pattern = "/^handle/";
					preg_match( $pattern, $name, $match );
					if( !empty( $match ) ) {
						$eventName = substr( $name, 6);
						$handlers[ $eventName ][] = $className;
					}
				}
			}
      }

      return $handlers;
   }
}

 ?>
