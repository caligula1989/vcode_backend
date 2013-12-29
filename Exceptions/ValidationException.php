<?php

class ValidationException extends Exception {

    private $field;
    private $validation;
    private $value;
    private $model;

    function __construct($field, $value, $validation, $model) {
        $this->field = $field;
        $this->value = $value;
        $this->validation = $validation;
        $this->model = $model;
        $value = is_object($value) ? get_class($value) : $value;
        $this->message = "Validation failed for field: `" . $this->field . "`. Value `" . $value . "` did not pass: '" . $this->validation . "' in model `" . $model . "`";
    }

}
