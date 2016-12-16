<?php

namespace App\Gnucash\Input;

use Illuminate\Http\Request;

class InputValidator
{
    protected $converter;
    protected $fields = array();
    protected $errors = array();

    public function __construct(Request $request)
    {
        $this->converter = new InputConverter($request);
    }

    public function requireField($field, Callable $getter)
    {
        if (is_null($value = $getter($this->converter))) {
            $this->errors[] = 'Input error: ' . $field;
        }

        $this->fields[$field] = $value;
    }

    public function getField($field)
    {
        return @ $this->fields[$field];
    }

    public function ready()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}