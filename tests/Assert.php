<?php

class AssertionException extends Exception {}

class Assert
{
    public static function assertTrue(bool $condition, string $message = 'Assertion failed'): void
    {
        if (!$condition) {
            throw new AssertionException("$message: Expected true, got false");
        }
    }

    public static function assertFalse(bool $condition, string $message = 'Assertion failed'): void
    {
        if ($condition) {
            throw new AssertionException("$message: Expected false, got true");
        }
    }

    public static function assertEquals($expected, $actual, string $message = 'Assertion failed'): void
    {
        if ($expected !== $actual) {
            $expectedStr = var_export($expected, true);
            $actualStr = var_export($actual, true);
            throw new AssertionException("$message: Expected $expectedStr, got $actualStr");
        }
    }

    public static function assertNotEquals($expected, $actual, string $message = 'Assertion failed'): void
    {
        if ($expected === $actual) {
            $expectedStr = var_export($expected, true);
            throw new AssertionException("$message: Expected not to be $expectedStr");
        }
    }

    public static function assertNull($actual, string $message = 'Assertion failed'): void
    {
        if ($actual !== null) {
            $actualStr = var_export($actual, true);
            throw new AssertionException("$message: Expected null, got $actualStr");
        }
    }

    public static function assertNotNull($actual, string $message = 'Assertion failed'): void
    {
        if ($actual === null) {
            throw new AssertionException("$message: Expected not null, got null");
        }
    }

    public static function assertInstanceOf(string $expected, $actual, string $message = 'Assertion failed'): void
    {
        if (!($actual instanceof $expected)) {
            $actualType = is_object($actual) ? get_class($actual) : gettype($actual);
            throw new AssertionException("$message: Expected instance of $expected, got $actualType");
        }
    }

    public static function assertContains($needle, array $haystack, string $message = 'Assertion failed'): void
    {
        if (!in_array($needle, $haystack)) {
            throw new AssertionException("$message: Array does not contain expected value");
        }
    }

    public static function assertCount(int $expected, array $actual, string $message = 'Assertion failed'): void
    {
        $actualCount = count($actual);
        if ($actualCount !== $expected) {
            throw new AssertionException("$message: Expected count $expected, got $actualCount");
        }
    }

    public static function assertStringContains(string $needle, string $haystack, string $message = 'Assertion failed'): void
    {
        if (strpos($haystack, $needle) === false) {
            throw new AssertionException("$message: String '$haystack' does not contain '$needle'");
        }
    }

    public static function expectException(string $exceptionClass, callable $callback, string $message = 'Expected exception was not thrown'): void
    {
        try {
            $callback();
            throw new AssertionException("$message: Expected $exceptionClass to be thrown");
        } catch (Exception $e) {
            if (!($e instanceof $exceptionClass)) {
                throw new AssertionException("$message: Expected $exceptionClass, got " . get_class($e));
            }
        }
    }
}
