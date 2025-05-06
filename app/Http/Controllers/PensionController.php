<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pension;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class PensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Pension::with('documents', 'salaries', 'client', 'user')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $client = Client::find($request->client_id);
        $age = Carbon::parse($client->date_of_birth)->age;

        $client->pensions()->delete();

        $stazh = $client->salaries()->count();
        $normalStazh = $client->salaries()->get()->sum(function ($salary) {
            return $salary->has_benefit ? 2 : 1;
        });

        if (($age < 60 && $client->benefits()->count() === 0) && $normalStazh / 12 < 32) {
            if ($normalStazh / 12 < 32 && $client->benefits()->count() === 0) {
                return response()->json([
                    'message' => 'Client should have more than or equal 32 years of stazh',
                ], 422);
            }

            return response()->json([
                'message' => 'Client should be older than 60 years old',
                'age' => $age,
            ], 422);
        }

        $averageSalaries = DB::table('average_salaries')->get()->toArray();

        $totalCoefficient = 0;

        foreach ($client->salaries()->get()->toArray() as $salary) {
            $year = (int)date('Y', strtotime($salary['date']));

            $averageSalary = null;
            foreach ($averageSalaries as $avgsalary) {
                if ($avgsalary->year === $year) {
                    $averageSalary = $avgsalary->value;
                    break;
                }
            }

            if (!$averageSalary) {
                $averageSalary = $averageSalaries[0]->value;
            }

            $coefficient = $salary['salary'] / $averageSalary;
            if ($salary['has_benefit']) {
                $totalCoefficient += $coefficient; // 2 раза додаємо коєфіцієнт, якщо екстримальні умови
            }

            $totalCoefficient += $coefficient;
        }

        $indKoef = $totalCoefficient / $stazh;

        $avgSalaryForLast3Years = $client->salaries()->whereYear('date', '>=', date('Y') - 3)->avg('salary');

        $pension = ($avgSalaryForLast3Years * $indKoef) * ($stazh / 12 * 0.01);

        if ($client->benefits()->count() > 0) {
            if ($client->benefits()->first()->value > $pension) {
                $pension = $client->benefits()->first()->value;
            }
        }

        $pension = Pension::create([
            'client_id' => $request->client_id,
            'user_id' => $request->user_id,
            'pension' => $pension,
        ]);

        return response()->json([
            'pension' => $pension
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pension $pension)
    {
        return $pension->with('documents', 'salaries', 'client', 'user');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pension $pension)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pension $pension)
    {
        $pension->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
