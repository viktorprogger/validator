<?php

interface RuleInterface
{
    public function validateAttribute(ValidatableInterface $validatable, string $attribute): bool;

    public function actualFor(string $scenario): bool;

    public function getErrors(): array;
}
