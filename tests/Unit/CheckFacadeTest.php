<?php

use Tarzancodes\RolesAndPermissions\Facades\Check;

it('can check that all values in an array exists in another', function () {
    $array = ['foo', 'bar', 'baz'];
    $other = ['foo', 'bar', 'baz', 'qux'];

    expect(Check::all($array)->existsIn($other))->toBeTrue();
    expect(Check::all($other)->existsIn($array))->toBeFalse();
});
