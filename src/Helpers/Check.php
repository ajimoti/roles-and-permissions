<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

class Check
{
    protected array $needle;

    /**
     * Array of values to check for
     * @param array $needle
     *
     * @return self
     */
    public function forAll(array $needle): self
    {
        $this->needle = $needle;

        return $this;
    }

    /**
     * Array of values to search in
     * @param array $haystack
     *
     * @return bool
     */
    public function in(array $haystack): bool
    {
        return empty(array_diff($this->needle, $haystack));
    }
}
