<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function contratPdf(Contrat $contrat): string
    {
        return Pdf::loadView('pdf.contrat', compact('contrat'))->output();
    }

    public function receiptPdf(Receipt $receipt): string
    {
        return Pdf::loadView('pdf.receipt', compact('receipt'))->output();
    }
}
