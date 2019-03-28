<?php

interface RuleInterface
{
    public function validateAttribute(ValidatableInterface $validatable, string $attribute): bool;

    public function actualFor(string $scenario): bool;

    public function addError(string $attribute, string $error): void;

    public function getErrors(): array;
}
