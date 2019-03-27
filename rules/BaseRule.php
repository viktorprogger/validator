<?php

abstract class BaseRule implements RuleInterface
{
    /** @var Callable */
    public $when;

    /** @var ValidatableInterface */
    public $validatable;

    /** @var string[] */
    public $on = [];

    protected $errors = [];

    public $message = '';

    public function actualFor(string $scenario): bool
    {
        $scenarios = (array)$this->on;
        $scenarioActual =  empty($scenarios) || in_array($scenario, $scenarios, true);

        if ($scenarioActual && is_callable($this->when)) {
            return call_user_func($this->when, $this->validatable);
        }

        return $scenarioActual;
    }

    public function addError(string $attribute, string $error): void
    {
        $this->errors[$attribute] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
