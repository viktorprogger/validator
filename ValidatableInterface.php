<?php

interface ValidatableInterface
{
    public function getScenario(): string;

    public function getValidationAttribute(string $name);

    public function rules(): array;
}
