<?php
 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (! $schema->hasColumn('users', 'third_nid')) {
            $schema->table('users', function (Blueprint $table) {
                $table->string('third_nid', 250)->after('id')->nullable();
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->table('users', function (Blueprint $table) {
            $table->dropColumn('third_nid');
        });
    }
];
