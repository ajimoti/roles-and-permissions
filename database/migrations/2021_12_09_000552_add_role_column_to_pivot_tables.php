<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleColumnToPivotTables extends Migration
{
    /**
     * All pivot table names
     *
     * @var array
     */
    protected array $table;

    /**
     * The name for the column that will be used to store role in the pivot table.
     *
     * @var array
     */
    protected string $roleColumnName;

    /**
     * Boot migration
     *
     */
    public function __construct()
    {
        $this->table = config('roles-and-permissions.pivot.tables');
        $this->roleColumnName = config('roles-and-permissions.pivot.column_name');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->table as $table) {
            // Only add the column if it doesn't exist
            if (! Schema::hasColumn($table, $this->roleColumnName)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string($this->roleColumnName)->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->table as $table) {
            // Only drop the column when it exists
            if (Schema::hasColumn($table, $this->roleColumnName)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn($this->roleColumnName);
                });
            }
        }
    }
}
