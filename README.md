# PHP validation library

### Draft concepts:

- Use [Yii2 validation system](https://github.com/yiisoft/yii2/tree/master/framework/validators) as a basis
- Rename concrete "validators" to validation "rules" (to exclude ambiguity in naming)
- Allow to validate any object with fulfillment of these 2 conditions:
    - implement `ValidatableInterface` within the object
    - pass the object to a new instance of `Validator` class
- Allow to validate any value through passing it to a `validateValue` method of a concrete validator (*not implemented*)
- Allow to use built-in validators (the same as in yii2)
- Allow to redefine built-in validators (see [`RuleFactory::set()`](RuleFactory.php#L11))
- Allow to use your own validators implemented:
    - as a class (must implement `RuleInterface`)
    - as an any `Callable`
- Unify the process of creating validation rules with `RuleFactory::get()`
    
### Usage examples:
```php
function rules(): array
{
    return [
        [
            'attributeName', 
            'match', 
            'on' => 'scenario1',
            'when' => [$this, 'needValidationRule1'],
            'pattern' => "/\d+/",
            'message' => 'I\'m a message about the validation is failed',
        ],
        [['attribute1', 'attribute2'], 'match', 'pattern' => '/\d+/'],
        [
            'attributeName', function(ValidatableInterface $validatable, string $attribute, RuleInterface $rule) 
            {
                if ($validatable->$attribute === 'test') {
                    $rule->addError($attribute, 'The value mustn\'t be "test"');
                    
                    return false;
                }
                
                return true;
            }
        ],
        ['attributeName', $this->validator->getFactory()->get('match', ['pattern' => '/\d+/'])],
        ['attributeName', [SomeClass::class, 'method'], 'param1' => 'value1'],
    ];
}
```

# Dependencies:
The only dependency I used is `yiisoft/di` to use `\yii\di\Factory::create()` method. I think it's an overhead to use the whole library to use just a single method but I didn't find a better way. Please make suggestions how to exclude this overhead.
