<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Salary::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $salary = new Salary([
            'client_id' => $request->client_id,
            'salary' => $request->salary,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'company_name' => $request->company_name,
            'job_title' => $request->job_title,
            'has_benefit' => $request->has_benefit,
        ]);

        $salary->save();

        return $salary;
    }

    /**
     * Display the specified resource.
     */
    public function show(Salary $salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salary $salary)
    {
        $salary->fill($request->all());

        $salary->save();

        return $salary;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salary $salary)
    {
        //
    }

    public function destroyAll(Request $request)
    {
        Salary::where('client_id', $request->client_id)->delete();
    }
}
