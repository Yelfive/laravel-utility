<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-20
 */

namespace fk\utility\Http;

use Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use fk\http\StatusCode as HttpStatusCode;

/**
 * @method $this code(int $code = 200)
 * @method $this message(string $message)
 * @method $this data(array $data)
 * @method $this list(array $data)
 * @method $this extend(array $data)
 * @method $this overrideExtend(array $data) Override the extend field
 *
 * @property int $code
 * @property string $message
 * @property array $data
 * @property array $list
 * @property array $extend
 */
class Result implements Jsonable
{

    /**
     * @var array
     */
    public $errors;

    /**
     * @var array
     * - `rules`    based on Laravel [[Illuminate\Validation\Validator]]
     *
     * About rules:
     * @see \Illuminate\Validation\Validator
     * @link https://laravel.com/docs/5.4/validation#available-validation-rules
     *
     */
    protected $rules = [
        'code' => 'required|integer|min:100',
        'message' => 'required|string',
        'data' => 'array',
        'list' => 'array',
        'extend' => 'array',
    ];

    // todo: not working
    protected $messages = [
        'message' => [
            'required' => '`message` is required for output.'
        ]
    ];

    protected $defaultValues = [
        'code' => 200,
    ];

    protected $result = [];

    protected $codeInBody = true;

    public function __construct($codeInBody = true)
    {
        $this->codeInBody = $codeInBody;
        $this->loadDefaultValues();
    }

    protected function loadDefaultValues()
    {
        foreach ($this->defaultValues as $k => $defaultValue) {
            $this->result[$k] = $defaultValue;
        }
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     * @throws Exception
     */
    public function toJson($options = 0)
    {
        if ($this->validate()) {
            $data = $this->toArray();
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception($this->errorsToString());
        }
    }

    public function errorsToString()
    {
        $errorString = '';
        foreach ($this->errors as $errors) {
            foreach ($errors as $error) {
                $errorString .= $error;
            }
        }
        return $errorString;
    }

    public function toArray()
    {
        $data = $this->result;
        if (isset($data['extend'])) {
            $extend = $data['extend'];
            unset($data['extend']);
            $data = array_merge($data, $extend);
        }

        if (!$this->codeInBody) unset($data['code']);

        return $data;
    }

    /**
     * Validate the whole result
     * @return bool
     */
    public function validate(): bool
    {
        $validator = Validator::make($this->result, $this->rules, $this->messages);
        if ($validator->passes()) {
            return true;
        } else {
            $this->errors = $validator->errors()->toArray();
            return false;
        }
    }

    public function __call($name, $arguments)
    {
        if (isset($this->rules[$name]) || $name === 'overrideExtend') {
            $value = $arguments[0] ?? null;
            if ($name === 'extend') {
                $value = array_merge($this->result['extend'] ?? [], $value);
            }
            if ($name === 'overrideExtend') $name = 'extend';
            $this->setAttribute($name, $value);
            return $this;
        }
        throw new Exception('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
    }

    public function __get($name)
    {
        return $this->result[$name] ?? null;
    }

    /**
     * Set
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    protected function setAttribute(string $name, $value)
    {
        $rule = $this->rules[$name];
        if ($value === null) {
            if (isset($this->defaultValues[$name])) {
                $value = $this->defaultValues[$name];
            } else {
                throw new Exception("Missing value for '$name', it must not be `null`");
            }
        }

        $validator = Validator::make([$name => $value], [$name => $rule]);
        if ($validator->fails()) {
            throw new Exception($validator->errors());
        } else {
            $this->result[$name] = $value;
        }
    }

    public function isEmpty(): bool
    {
        return !$this->result;
    }

    /**
     * Return a message after form validation failed
     * @param array|MessageBag $errors
     * @param null|string $message
     * @return static
     */
    public static function validationFailed($errors, $message = null): Result
    {
        return static::instance()
            ->code(HttpStatusCode::CLIENT_UNPROCESSABLE_ENTITY)
            ->message($message ?? __('base.Invalid params.'))
            ->extend(['extra' => $errors]);
    }

    /**
     * @param bool $codeInBody
     * @return Result
     */
    public static function instance(bool $codeInBody = true)
    {
        return new static($codeInBody);
    }
}