<?php

declare(strict_types = 1);

namespace App\Enum;

use ReflectionClass;
use RuntimeException;
use ReflectionException;
use App\Exception\InvalidEnumNameException;
use App\Exception\InvalidEnumValueException;

abstract class Enum
{
    /**
     * keep in memory constant discovered (optimisation)
     */
    private static ?array $constCacheArray = [];

    public static function getAll(): array
    {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            try {
                $current = new ReflectionClass($calledClass);

                self::$constCacheArray[$calledClass] = array_diff(
                    $current->getConstants(),
                    $current->getParentClass()->getConstants()
                );
            } catch (ReflectionException $e) {
                // avoid throwing reflection exception
                throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName(string $name): bool
    {
        $constants = self::getAll();
        return array_key_exists($name, $constants);
    }

    public static function isValidValue(string $value): bool
    {
        $values = array_values(self::getAll());
        return in_array($value, $values, true);
    }

    /**
     * @throws InvalidEnumNameException
     */
    public static function validateName(string $name): void
    {
        if (!self::isValidName($name)) {
            throw new InvalidEnumNameException($name, get_called_class());
        }
    }

    /**
     * @throws InvalidEnumValueException
     */
    public static function validateValue(string $value): void
    {
        if (!self::isValidValue($value)) {
            throw new InvalidEnumValueException($value, get_called_class());
        }
    }
}
