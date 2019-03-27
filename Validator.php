<?php

class Validator
{
    /** @var RuleInterface[][] */
    private $map;
    /** @var RuleFactory */
    private $factory;
    private $errors = [];
    /** @var RuleInterface[] */
    private $rules = [];

    public function __construct(RuleFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param ValidatableInterface $validatable
     *
     * @return bool
     * @throws Exception
     */
    public function validate(ValidatableInterface $validatable): bool
    {
        $this->createMap($validatable);
        $result = true;

        foreach ($this->map as $attribute => $rules) {
            foreach ($rules as $rule) {
                $result = $result && $rule->validateAttribute($validatable, $attribute);
            }
        }

        return $result;
    }

    public function getErrors(): array
    {
        if (empty($this->errors)) {
            /** @var RuleInterface[] $rules */
            $rules = array_unique(array_values($this->rules));
            foreach ($rules as $rule) {
                foreach ($rule->getErrors() as $attribute => $error) {
                    if (!isset($this->errors[$attribute])) {
                        $this->errors[$attribute] = [];
                    }

                    $this->errors[$attribute][] = $error;
                }
            }
        }

        return $this->errors;
    }

    /**
     * @param ValidatableInterface $validatable
     *
     * @throws Exception
     */
    private function createMap(ValidatableInterface $validatable): void
    {
        $configs = $validatable->rules();
        $scenario = $validatable->getScenario();

        foreach ($configs as $config) {
            [$attributes, $type, $config] = $config;
            $config['validatable'] = $validatable;
            $rule = $this->factory->get($type, $config);

            if ($rule->actualFor($scenario)) {
                foreach ($attributes as $attribute) {
                    $this->map[$attribute][] = $rule;

                    if (!in_array($rule, $this->rules)) {
                        $this->rules[] = $rule;
                    }
                }
            }
        }
    }
}
