<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;
use Illuminate\Support\Collection;

abstract class BaseEnum extends Enum
{
    /**
     * The class used to wrap the values when the `collect()` method is called.
     *
     * @var Collection
     */
    protected static $collectionClass = Collection::class;

    /**
     * Return a collection of the enum
     *
     * @return array
     */
    public static function getInstanceFromValues(...$values): array
    {
        $values = collect($values)->flatten()->all();

        foreach ($values as $value) {
            $valueObject[] = new static($value);
        }

        return $valueObject ?? [];
    }

    /**
     * Return a Collection of the values.
     *
     * @return Collection
     */
    public static function collect(...$values): Collection
    {
        $values = collect($values)->flatten()->all();

        return new static::$collectionClass(static::getInstanceFromValues($values));
    }
}
