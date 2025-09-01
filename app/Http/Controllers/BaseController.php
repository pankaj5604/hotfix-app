<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller as LaravelController;

class BaseController extends LaravelController
{
    /**
     * Format Laravel validation errors
     */
    protected function formatValidationErrors(Validator $validator)
    {
        $errors = [];

        foreach ($validator->errors()->getMessages() as $field => $messages) {
            foreach ($messages as $message) {
                $failedRules = $validator->failed()[$field] ?? [];

                foreach ($failedRules as $rule => $ruleInfo) {
                    $errors[] = [
                        'code'    => strtolower($rule),  // rule name like required, unique
                        'message' => $message,           // message from validator
                        'field'   => $field,             // field name
                    ];
                }
            }
        }

        return $errors;
    }

    /**
     * Custom validation failed response
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $this->formatValidationErrors($validator);

        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $errors
        ], 422));
    }

    /**
     * Manually send error
     */
    protected function sendError($code, $message, $field = null, $statusCode = 400)
    {
        $error = [
            'code'    => $code,
            'message' => $message,
            'field'   => $field,
        ];

        return response()->json([
            'status' => false,
            'errors' => [$error]
        ], $statusCode);
    }
}
