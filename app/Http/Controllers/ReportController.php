<?php

namespace App\Http\Controllers;

use App\Models\Search;
use App\Models\Sport;
use App\Services\ReportGenerator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct(private ReportGenerator $generator) {}

    //gera o relatório via IA
    public function generate(Search $search)
    {
        abort_if($search->user_id !== auth()->id(), 403);

        try {
            $this->generator->generate($search);
            return redirect()
                ->route('report.show', $search)
                ->with('success', 'Relatório gerado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    //exibe o relatório na tela
    public function show(Search $search)
    {
        abort_if($search->user_id !== auth()->id(), 403);

        $report = $search->report;

        if (!$report) {
            return redirect()
                ->route('results.show', $search)
                ->with('error', 'Relatório ainda não gerado.');
        }

        return view('results.report', compact('search', 'report'));
    }

    //gera e baixa o PDF
    public function pdf(Search $search)
    {
        abort_if($search->user_id !== auth()->id(), 403);

        $report = $search->report;

        if (!$report) {
            return redirect()
                ->route('results.show', $search)
                ->with('error', 'Gere o relatório IA antes de baixar o PDF.');
        }

        $climateData = $search->climateData()->orderBy('month')->get();
        $sport       = Sport::where('name', $search->getRawOriginal('sport'))->first();

        $pdf = Pdf::loadView('results.report-pdf', compact('search', 'report', 'climateData', 'sport'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'       => 'DejaVu Sans',
                'isRemoteEnabled'   => false,
                'isHtml5ParserEnabled' => true,
                'dpi'               => 150,
            ]);

        $filename = 'tactical-heat-' . str($search->city_name)->slug() . '-' . $search->year . '.pdf';

        return $pdf->download($filename);
    }
}