<?php

class InvalidOperationException extends CosmoMongerException
{
    public function __construct($message) {
		parent::__construct($message);
    }
}