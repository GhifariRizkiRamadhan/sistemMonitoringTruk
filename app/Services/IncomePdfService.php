<?php

namespace App\Services;

use TCPDF;
use Carbon\Carbon;

class IncomePdfService
{
    public function generate($data, $filename, $chart_data, $month, $year, $truck_info, $total_ongkos, $total_uang_jalan, $total_hasil)
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('PT Harry');
        $pdf->SetAuthor('PT Harry');
        $pdf->SetTitle('Laporan Pendapatan');
        $pdf->SetSubject('Laporan Pendapatan Truk');

        // Remove header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(10, 15, 10);

        // Add page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', 'B', 14);

        // Title
        $bulan = Carbon::createFromFormat('m', $month)->format('F');
        
        if ($truck_info) {
            $category_text = ($truck_info->category == 'own') ? 'Milik Sendiri' : 'TEP';
            $title = "Laporan Pendapatan - Truk {$truck_info->plate_number} ($category_text)";
        } else {
            $title = "Laporan Pendapatan - Semua Truk";
        }

        $title .= " - $bulan $year";
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(5);

        // Summary
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(90, 10, 'Total Angkutan: ' . $chart_data['total_shipments'] . ' Trip', 1, 0, 'C', true);
        $pdf->Cell(90, 10, 'Total Ongkos Angkut: Rp ' . number_format($chart_data['total_transport_cost'], 0, ',', '.'), 1, 0, 'C', true);
        $pdf->Cell(90, 10, 'Total Hasil: Rp ' . number_format($chart_data['total_results'], 0, ',', '.'), 1, 1, 'C', true);
        $pdf->Ln(5);

        // Table header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(52, 152, 219);
        $pdf->SetTextColor(255);

        $headers = array_keys($data[0] ?? []);
        $widths = [20, 25, 25, 25, 25, 15, 20, 30, 30, 30];

        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($widths[$i], 10, $headers[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Table data
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 8);

        $fill = false;
        foreach($data as $row) {
            $i = 0;
            foreach($row as $value) {
                $pdf->Cell($widths[$i], 8, $value, 1, 0, 'C', $fill);
                $i++;
            }
            $pdf->Ln();
            $fill = !$fill;
        }

        // Add totals
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(array_sum(array_slice($widths, 0, 7)), 8, 'Total:', 1, 0, 'R', true);
        $pdf->Cell($widths[7], 8, 'Rp ' . number_format($total_ongkos, 0, ',', '.'), 1, 0, 'C', true);
        $pdf->Cell($widths[8], 8, 'Rp ' . number_format($total_uang_jalan, 0, ',', '.'), 1, 0, 'C', true);
        $pdf->Cell($widths[9], 8, 'Rp ' . number_format($total_hasil, 0, ',', '.'), 1, 0, 'C', true);

        // Add print date
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 0, 'R');

        // Output PDF
        return response()->streamDownload(function() use ($pdf, $filename) {
            echo $pdf->Output($filename . '.pdf', 'S');
        }, $filename . '.pdf');
    }
}