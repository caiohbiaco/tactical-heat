<?php

namespace App\Http\Controllers;

use App\Models\Search;
use App\Models\Sport;
use App\Models\ClimateData;
use App\Services\ClimateService;
use App\Services\RiskAnalyzer;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private ClimateService $climate,
        private RiskAnalyzer $risk
    ) {}

    public function index()
    {
        $searches = auth()->user()
            ->searches()
            ->latest()
            ->paginate(10);

        return view('dashboard', compact('searches'));
    }

    public function create()
    {
        $sports = Sport::orderBy('name')->get();
        return view('search.form', compact('sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'city'  => 'required|string',
            'sport' => 'required|exists:sports,name',
            'year'  => 'required|integer|min:1940|max:' . date('Y'),
        ]);

        try {
            // Se lat/lng vieram do autocomplete, usa direto sem chamar geocoding
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $coords = [
                    'name'      => $request->city,
                    'latitude'  => (float) $request->latitude,
                    'longitude' => (float) $request->longitude,
                ];
            } else {
                $coords = $this->climate->getCoordinates($request->city);
            }

            $search = auth()->user()->searches()->create([
                'city_name' => $coords['name'],
                'latitude'  => $coords['latitude'],
                'longitude' => $coords['longitude'],
                'sport'     => $request->sport,
                'year'      => $request->year,
            ]);

            $monthlyData = $this->climate->getYearlyClimate(
                $coords['latitude'],
                $coords['longitude'],
                $request->year
            );

            $sport    = Sport::where('name', $request->sport)->first();
            $analyzed = $this->risk->analyzeSearch($monthlyData, $sport);

            foreach ($analyzed as $month) {
                ClimateData::create(array_merge(
                    array_diff_key($month, ['is_projection' => '']),
                    ['search_id' => $search->id]
                ));
            }

            return redirect()->route('results.show', $search);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao processar busca: ' . $e->getMessage());
        }
    }

    public function destroy(Search $search)
    {
        abort_if($search->user_id !== auth()->id(), 403);

        $search->climateData()->delete();
        $search->report()->delete();
        $search->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Análise removida com sucesso.');
    }
}