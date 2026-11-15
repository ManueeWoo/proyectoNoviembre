<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generatePdfFromTemplate(string $templatePath, array $data): string
    {
        $html = $this->renderTemplate($templatePath, $data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function renderTemplate(string $templatePath, array $data): string
    {
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
