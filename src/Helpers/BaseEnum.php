<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;
use Illuminate\Support\Collection;

abstract class BaseEnum extends Enum
{
    /**
     * A readable title of the enum.
     *
     * @var string
     */
    public string $title;

    /**
     * The class used to wrap the values when the `collect()` method is called.
     *
     * @var Collection
     */
    protected static $collectionClass = Collection::class;

    public function __construct($enumValue)
    {
        parent::__construct($enumValue);

        $this->title = static::getTitle($enumValue);
    }


    /**
     * Get all enum instances.
     *
     * @return Collection
     */
    final public static function all(): Collection
    {
        return new static::$collectionClass(
            static::getInstanceFromValues(static::getValues())
        );
    }

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

    /**
     * Get the enum as an array formatted for a select.
     *
     * [mixed $value => string title]
     *
     * @return array
     */
    public static function asSelectArray(): array
    {
        foreach(static::getInstances() as $instance) {
            $array[$instance->value] = $instance->title;
        }

        return $array ?? [];
    }

    public static function getTitle($value): string
    {
        return static::getFriendlyKeyName(static::getKey($value));
    }
}
