<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Tournament;

class PDFController extends Controller
{
    public function descargarPartidos(Tournament $tournament)
    {
        // Puedes aplicar aquí una autorización si quieres
        // $this->authorize('view', $tournament);

        $partidos = $tournament->matches()->with(['equipo1', 'equipo2'])->get();

        $pdf = Pdf::loadView('pdf.partidos', [
            'tournament' => $tournament,
            'partidos' => $partidos,
        ]);

        return $pdf->download('partidos_' . $tournament->nombre . '.pdf');
    }
}
