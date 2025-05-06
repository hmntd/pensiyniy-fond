<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\Client;
use App\Models\ClientBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Benefit::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Benefit $benefit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Benefit $benefit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Benefit $benefit)
    {
        //
    }

    public function addToUser(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        $client->benefits()->delete();

        if ($request->benefit_id) {
            ClientBenefit::create([
                'client_id' => $client->id,
                'benefit_id' => $request->benefit_id
            ]);
        }

        $client->load('benefits');
        return $client->benefits;
    }
}
