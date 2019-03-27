<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * RegularExpressionRule validates that the attribute value matches the specified [[pattern]].
 *
 * If the [[not]] property is set true, the validator will ensure the attribute value do NOT match the [[pattern]].
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RegularExpressionRule extends BaseRule
{
    /**
     * @var string the regular expression to be matched with
     */
    public $pattern;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via [[pattern]] should NOT match the attribute value.
     */
    public $not = false;


    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        if ($this->pattern === null) {
            throw new Exception('The "pattern" property must be set.');
        }
        if ($this->message === null) {
            $this->message ='Attribute is invalid.';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value): bool
    {
        $valid = !is_array($value) && (bool)preg_match($this->pattern, $value) !== $this->not;

        return $valid;
    }

    public function validateAttribute(ValidatableInterface $validatable, string $attribute): bool
    {
        $value = $validatable->getValidationAttribute($attribute);
        if (!$result = $this->validateValue($value)) {
            $this->addError($attribute, $this->message);
        }

        return $result;
    }
}
