<?php

namespace App\Http\Controllers;

use App\Models\Search;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function show(Search $search)
    {
        // Garante que o usuário só veja seus próprios dados
        abort_if($search->user_id !== auth()->id(), 403);

        $climateData = $search->climateData()->orderBy('month')->get();

        return view('results.show', compact('search', 'climateData'));
    }

    public function risk(Search $search)
    {
    abort_if($search->user_id !== auth()->id(), 403);

    $climateData = $search->climateData()->orderBy('month')->get();

    // Busca o objeto Sport explicitamente pelo nome salvo na coluna
    $sport = \App\Models\Sport::where('name', $search->getRawOriginal('sport'))->first();

    abort_if(!$sport, 404); // proteção caso o esporte não exista no banco

    return view('results.risk', compact('search', 'climateData', 'sport'));
    }
}

