<?php

namespace App\core;

use App\core\exceptions\NotFoundException;

class Validation
{
    public $errors = [];

    /**
     * List of validation rules, with their respective error messages
     */
    protected $availableRules = [
        'required' => 'Il campo :field: è obbligatorio.',
        'email' => 'Il campo :field: deve contenere un indirizzo email valido.',
        'alpha_dash' => 'Il campo :field: può contenere solo lettere, numeri, trattini e underscore.',
        'unique' => 'Il campo :field: è già presente nella tabella {table}.',
        'exists' => 'Il campo :field: non è presente nel database.',
        'min' => 'Il campo :field: non può essere di lunghezza inferiore a {min} caratteri.',
        'max' => 'Il campo :field: non può essere di lunghezza superiore a {max} caratteri.',
        'match' => 'Il campo :field: deve essere uguale al campo {match}.',
        'letter' => 'Il campo :field: deve contenere almeno una lettera.',
        'number' => 'Il campo :field: deve contenere almeno un numero.',
        'upper' => 'Il campo :field: deve contenere almeno una lettera maiuscola.',
        'special_char' => 'Il campo :field: deve contenere almeno un carattere speciale. !#$%&?@_'
    ];

    /**
     * @param array $data
     * @param array $rules
     * @param string $url
     * @return array
     */
    public function validate(array $data, array $rules, string $url): array
    {
        $sanitizedData = $this->sanitize($data);

        // Check that all the rules passed by the user are present in the availableRules array
        $this->ruleExists($rules);

        // Check for each field, and if there are any errors, they are added to the errors array
        $this->validateFields($rules, $sanitizedData);

        if (!empty($this->errors)) {

            Application::$app->response->redirect($url)
                ->withValidationErrors($this->errors)
                ->withOldData($data);

            exit;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return 
     */
    protected function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = stripslashes(trim(filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS)));
        }

        return $data;
    }

    /**
     * @param array $rules
     * @param array $data
     * @return void
     */
    protected function validateFields(array $rules, array $data): void
    {
        foreach ($rules as $field => $validationRules) {

            $fieldValue = [
                //  "username" => "valore input",
                //  "rules"    => "required,alpha_dash"
                $field => $data[$field],
                'rules' => implode(',', $validationRules)
            ];

            // Rule email
            if (str_contains($fieldValue['rules'], 'email') && !filter_var($fieldValue[$field], FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, 'email');
            }

            // Rule alpha_dash
            if (str_contains($fieldValue['rules'], 'alpha_dash') && !preg_match('/^[a-zA-Z0-9_-]*$/', $fieldValue[$field])) {
                $this->addError($field, 'alpha_dash');
            }

            // Rule unique
            if (str_contains($fieldValue['rules'], 'unique')) {

                $arr = explode(',', $fieldValue['rules']);

                $uniqueRule = array_filter($arr, function ($value) {
                    return str_contains($value, 'unique');
                });

                $table = explode(':', array_values($uniqueRule)[0])[1];

                /**
                 * If the rule has no flag (represented by the hyphen), I look in the DB if the value is already present.
                 * If it is, return error
                 **/
                if (!str_contains($table, '-')) {
                    $fieldInDb = Application::$app->builder
                        ->select()
                        ->from($table)
                        ->where($field, $fieldValue[$field])
                        ->first();

                    if ($fieldInDb) {
                        $this->addError($field, 'unique', ['table' => $table]);
                    }
                } else {
                    /**
                     * If the modem ID is also passed, I search through all records (other than the model passed as argument), if there is a value like the one passed
                     */
                    $modelId = trim(substr($table, strpos($table, '-')), '-');
                    $table = substr($table, 0, strpos($table, '-'));

                    $models = Application::$app->builder
                        ->select()
                        ->from($table)
                        ->where('id', $modelId, '!=')
                        ->get();

                    $columns = array_column($models, $field);

                    if (in_array($fieldValue[$field], $columns)) {
                        $this->addError($field, 'unique', ['table' => $table]);
                    }
                }
            }

            // Rule exists
            if (str_contains($fieldValue['rules'], 'exists')) {

                $arr = explode(',', $fieldValue['rules']);

                $existsRule = array_filter($arr, function ($value) {
                    return str_contains($value, 'exists');
                });

                $table = explode(':', array_values($existsRule)[0])[1];

                $fieldInDb = Application::$app->builder
                    ->select()
                    ->from($table)
                    ->where($field, $fieldValue[$field])
                    ->first();

                if (!$fieldInDb) {
                    $this->addError($field, 'exists', ['table' => $table]);
                }
            }

            // Rule min
            if (str_contains($fieldValue['rules'], 'min')) {

                $arr = explode(',', $fieldValue['rules']);

                $minRule = array_filter($arr, function ($value) {
                    return str_contains($value, 'min');
                });

                // Reindex with array_values ​​as the $ minRule index varies based on where it is in the rules array
                $min = explode(':', array_values($minRule)[0]);

                if (strlen($fieldValue[$field]) < $min[1]) {
                    $this->addError($field, 'min', ['min' => $min[1]]);
                }
            }

            // Rule max
            if (str_contains($fieldValue['rules'], 'max')) {

                $arr = explode(',', $fieldValue['rules']);

                $maxRule = array_filter($arr, function ($value) {
                    return str_contains($value, 'max');
                });

                $max = explode(':', array_values($maxRule)[0]);

                if (strlen($fieldValue[$field]) > $max[1]) {
                    $this->addError($field, 'max', ['max' => $max[1]]);
                }
            }

            // Rule match
            if (str_contains($fieldValue['rules'], 'match')) {

                $arr = explode(',', $fieldValue['rules']);

                $matchRule = array_filter($arr, function ($value) {
                    return str_contains($value, 'match');
                });

                $match = explode(':', array_values($matchRule)[0]);

                // If the value of the field with the match is different from the field that must match, I create an error
                if ($fieldValue[$field] !== $data[$match[1]]) {
                    $this->addError($field, 'match', ['match' => $match[1]]);
                }
            }

            // Rule letter
            if (str_contains($fieldValue['rules'], 'letter') && !preg_match('/[a-z]+/', $fieldValue[$field])) {
                $this->addError($field, 'letter');
            }

            // Rule number
            if (str_contains($fieldValue['rules'], 'number') && !preg_match('/[\d]+/', $fieldValue[$field])) {
                $this->addError($field, 'number');
            }

            // Rule upper
            if (str_contains($fieldValue['rules'], 'upper') && !preg_match('/[A-Z]+/', $fieldValue[$field])) {
                $this->addError($field, 'upper');
            }

            // Rule special_char
            if (str_contains($fieldValue['rules'], 'special_char') && !preg_match('/[!#$%&?@_]/', $fieldValue[$field])) {
                $this->addError($field, 'special_char');
            }

            // Rule required -> It must remain last in the controls, so as to have priority in the views
            if (str_contains($fieldValue['rules'], 'required') && !$fieldValue[$field]) {
                $this->addError($field, 'required');
            }
        }
    }

    /** 
     * @param array $validationRules
     * @return void
     * @throws NotFoundException
     */
    protected function ruleExists(array $rules): void
    {
        /** 
         *  Take the array values, es: ['required', 'alpha_dash] 
         *  Flat the array with array_merge
         *  Take only the unique values
         */
        $rules = array_unique(array_merge(...array_values($rules)));

        foreach ($rules as $rule) {

            // If it's a "compound" rule, I get all the characters before the:
            if (strpos($rule, ':')) {
                $rule = substr($rule, 0, strpos($rule, ':'));
            }

            if (!in_array($rule, array_keys($this->availableRules))) {
                throw new NotFoundException("Validation rule $rule doesn't exist");
            }
        }
    }

    /**
     * @param string $field
     * @param string $rule
     * @param array  $params
     * @return void
     */
    protected function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->availableRules[$rule];

        $message = str_replace(':field:', $field, $message);

        // If there are any parameters, I substitute them for the placeholder within the related error messages
        if ($params) {
            if (str_contains($message, "{" . array_keys($params)[0] . "}")) {
                $message = str_replace("{" . array_keys($params)[0] . "}", array_values($params)[0], $message);
            }
        }

        $this->errors[$field] = $message;
    }
}
