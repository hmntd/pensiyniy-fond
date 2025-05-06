<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $averageSalaries = [
            2000 => 230.99,
            2001 => 311.62,
            2002 => 375.98,
            2003 => 372.72,
            2004 => 524.14,
            2005 => 735.57,
            2006 => 928.81,
            2007 => 1197.91,
            2008 => 1573.99,
            2009 => 1650.43,
            2010 => 1982.63,
            2011 => 2370.53,
            2012 => 2752.95,
            2013 => 2979.46,
            2014 => 3149.45,
            2015 => 3661.41,
            2016 => 4482.35,
            2017 => 6273.45,
            2018 => 7810.88,
            2019 => 9205.19,
            2020 => 10340.35,
            2021 => 12993.56,
            2022 => 13376.21,
            2023 => 14308.46,
            2024 => 17486.60,
            2025 => 18660,32,
        ];

        foreach ($averageSalaries as $year => $value) {
            DB::table('average_salaries')->insert([
                'year' => $year,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
