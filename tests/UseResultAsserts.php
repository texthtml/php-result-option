<?php

declare(strict_types=1);

namespace Tests;

trait UseResultAsserts
{
    public static function use(mixed $value)
    {
        match(true) {
            $value instanceof \th\Option => $value->map([__CLASS__, __FUNCTION__]),
            $value instanceof \th\Result => $value->map([__CLASS__, __FUNCTION__])->isOk(),
            default => $value,
        };
    }

    public static function assertEquals($expected, $actual, string $message = ''): void
    {
        static::use($expected);
        static::use($actual);

        parent::assertEquals($expected, $actual, $message);
    }

    public static function assertNotEquals($expected, $actual, string $message = ''): void
    {
        static::use($expected);
        static::use($actual);

        parent::assertNotEquals($expected, $actual, $message);
    }

    public static function assertSame($expected, $actual, string $message = ''): void
    {
        static::use($expected);
        static::use($actual);

        parent::assertSame($expected, $actual, $message);
    }

    public static function assertNotSame($expected, $actual, string $message = ''): void
    {
        static::use($expected);
        static::use($actual);

        parent::assertNotSame($expected, $actual, $message);
    }
}
