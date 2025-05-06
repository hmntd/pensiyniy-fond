<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Client::with('salaries', 'benefits')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $client = new Client([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_id' => $request->user_id,
            'date_of_birth' => Carbon::parse($request->date_of_birth)->format('Y-m-d'),
        ]);

        $client->save();

        return $client;
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $client->update($request->all());
        $client->save();

        return $client;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
