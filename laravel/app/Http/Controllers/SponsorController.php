<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{

    public function index()
    {
        return response()->json(Sponsor::all(), 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric',
        ]);

        $sponsor = Sponsor::create($validated);

        return response()->json($sponsor, 201);
    }


    public function show(Sponsor $sponsor)
    {
        return response()->json($sponsor, 200);
    }


    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:191',
            'amount' => 'sometimes|numeric',
        ]);

        $sponsor->update($validated);

        return response()->json($sponsor, 200);
    }


    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();

        return response()->json(['message' => 'Sponsor deleted successfully'], 200);
    }
}
