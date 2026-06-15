<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Services\ClimateService;
use App\Services\RiskAnalyzer;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function __construct(
        private ClimateService $climate,
        private RiskAnalyzer $risk
    ) {}

    public function form()
    {
        $sports = Sport::all();
        return view('compare.form', compact('sports'));
    }

    public function result(Request $request)
    {
        $request->validate([
            'city_a' => 'required|string',
            'city_b' => 'required|string',
            'year_a' => 'required|integer|min:1940|max:' . (date('Y') - 1),
            'year_b' => 'required|integer|min:1940|max:' . (date('Y') - 1),
            'sport'  => 'required|exists:sports,name',
        ]);

        try {
            $sport = Sport::where('name', $request->sport)->first();

            // Cidade A
            $coordsA  = $this->climate->getCoordinates($request->city_a);
            $monthlyA = $this->climate->getYearlyClimate($coordsA['latitude'], $coordsA['longitude'], $request->year_a);
            $dataA    = $this->risk->analyzeSearch($monthlyA, $sport);

            // Cidade B
            $coordsB  = $this->climate->getCoordinates($request->city_b);
            $monthlyB = $this->climate->getYearlyClimate($coordsB['latitude'], $coordsB['longitude'], $request->year_b);
            $dataB    = $this->risk->analyzeSearch($monthlyB, $sport);

            $cityA = [
                'name' => $coordsA['name'],
                'lat'  => $coordsA['latitude'],
                'lng'  => $coordsA['longitude'],
                'year' => $request->year_a,
                'data' => array_values($dataA),
            ];

            $cityB = [
                'name' => $coordsB['name'],
                'lat'  => $coordsB['latitude'],
                'lng'  => $coordsB['longitude'],
                'year' => $request->year_b,
                'data' => array_values($dataB),
            ];

            return view('compare.result', compact('cityA', 'cityB', 'sport'));

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao buscar dados: ' . $e->getMessage());
        }
    }
}