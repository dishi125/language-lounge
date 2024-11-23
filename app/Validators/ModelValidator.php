<?php 

namespace App\Validators;
use Validator;

abstract class ModelValidator
{
    protected function validateLaravelRules($input = [], $rules = [])
    {
        return Validator::make($input->toArray(), $rules);
    }
}