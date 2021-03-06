<?php

class ArgumentException extends CosmoMongerException {
    private $paramName;

    public function __construct($message, $paramName) {
    //parent::__construct("$message. Argument: $paramName");
        parent::__construct($message);
        $this->paramName = $paramName;
    }

    public function getParamName() {
        return $this->paramName;
    }
}