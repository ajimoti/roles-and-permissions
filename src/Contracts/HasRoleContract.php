<?php

namespace Tarzancodes\RolesAndPermissions\Contracts;

use Illuminate\Contracts\Auth\Access\Authorizable;

interface HasRoleContract extends Authorizable
{
    public function has(...$permissions): bool;

    public function authorize(...$permissions): bool;

    public function permissions(): array;

    public function assign(string $role): bool;

    public function removeRole(): bool;
}
