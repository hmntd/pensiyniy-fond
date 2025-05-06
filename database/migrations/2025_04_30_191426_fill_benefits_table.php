<?php

use App\Models\Benefit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $data = [
            [
                'name' => 'Invalidity I',
                'value' => 8839.03,
            ],
            [
                'name' => 'Invalidity II',
                'value' => 7071.36,
            ],
            [
                'name' => 'Invalidity III',
                'value' => 5450.85,
            ],
            [
                'name' => 'Victim of the Chernobyl disaster I',
                'value' => 14308.46,
            ],
            [
                'name' => 'Victim of the Chernobyl disaster II',
                'value' => 11446.77,
            ],
            [
                'name' => 'Participant in hostilities',
                'value' => 4958,
            ],
        ];

        foreach ($data as $benefit) {
            Benefit::create([
                'name' => $benefit['name'],
                'type' => 'static',
                'value' => $benefit['value'],
            ]);
        }

        Benefit::create([
            'name' => 'Extreme work',
            'type' => 'coeff',
            'coefficient' => 2,
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
