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

require_once( "AggregateRootBuilder.php" );
require_once( "DomainEvent.php" );

abstract class AggregateRoot {

	abstract public function __construct( array $args, bool $isNew );

	abstract public static function getProperties();

	static public function hydrate( array $args ) {
		return new self( $args, false );
	}
	
	static public function new( array $args ) {
		return new self( $args, true );
	}

	final public static function getBuilder() {
		$class = get_called_class();
		return new AggregateRootBuilder( $class );
	}

	public function getUncommittedChanges() {
		return $this->changes;
	}

	public function markChangesAsCommitted() {
		$this->changes = array();
	}

	protected function applyChange( DomainEvent $event ) {
		print_r( $event );
		$this->changes[] = $event;
	}

	protected function get( $key ) {
		return $this->vars[$key];
	}

	protected function set( $key, $value ) {
		$this->vars[$key] = $value;
	}

	private $changes = array();

	private $vars = array();

	protected $uuid;

	protected $version;
}

 ?>
