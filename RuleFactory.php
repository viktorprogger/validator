<?php

use yii\di\Factory;

class RuleFactory
{
    private $definitions = [
        'match' => RegularExpressionRule::class,
    ];

    public function set(string $id, string $class): void
    {
        $this->definitions[$id] = $class;
    }

    /**
     * @param mixed $rule
     * @param array $params
     *
     * @return RuleInterface
     * @throws \yii\di\exceptions\InvalidConfigException
     * @throws \yii\di\exceptions\NotInstantiableException
     * @throws \Exception
     */
    public function get($rule, array $params = []): RuleInterface
    {
        $result = false;

        if ($rule instanceof RuleInterface) {
            return $rule;
        }

        if (empty($params['validatable']) || !$params['validatable'] instanceof ValidatableInterface) {
            throw new Exception('Config must have a "validatable" key with a ValidatableInterface object');
        }

        if (is_callable($rule)) {
            $result = $this->fromCallable($rule, $params);
        }

        if (is_string($rule)) {
            if (array_key_exists($rule, $this->definitions)) {
                $rule = $this->definitions[$rule];
            }

            $result = (new Factory())->create($rule, $params);
        }

        if (!$result) {
            throw new Exception('Can\'t instantiate the given validator');
        }

        if (!$result instanceof RuleInterface) {
            throw new Exception('Validator must implement RuleInterface');
        }

        return $result;
    }

    private function fromCallable($callable, $params)
    {
        return new class($callable, $params) extends BaseRule
        {
            private $callable;
            public $params;

            public function __construct(Callable $callable, $params)
            {
                $this->callable = $callable;
                $this->params = $params;
            }

            public function validateAttribute(ValidatableInterface $validatable, string $attribute): bool
            {
                return call_user_func($this->callable, $validatable, $attribute, $this);
            }
        };
    }
}
