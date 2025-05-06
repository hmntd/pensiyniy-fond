<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $data = [
            'Admin',
            'User',
            'Employee',
        ];

        foreach ($data as $role) {
            Role::create([
                'role_name' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
