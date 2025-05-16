<?php
namespace App\Services;

use TCPDF;
use Carbon\Carbon;

class FinancialPdfService
{
    public function generate($data, $filename, $total_income, $total_expense, $total_net_income, $month, $year, $truck_info)
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('PT Harry');
        $pdf->SetAuthor('PT Harry');
        $pdf->SetTitle('Laporan Keuangan');
        $pdf->SetSubject('Laporan Keuangan Truk');
        
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
            $title = "Laporan Keuangan - Truk {$truck_info->plate_number} ($category_text)";
        } else {
            $title = "Laporan Keuangan - Semua Truk";
        }
        $title .= " - $bulan $year";
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Summary section
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Ringkasan Keuangan', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(240, 240, 240);
        
        // Summary data
        $pdf->Cell(80, 8, 'Total Penghasilan Kotor', 1, 0, 'L', true);
        $pdf->Cell(80, 8, 'Rp ' . number_format($total_income, 0, ',', '.'), 1, 1, 'R');
        
        $pdf->Cell(80, 8, 'Total Pengeluaran', 1, 0, 'L', true);
        $pdf->Cell(80, 8, 'Rp ' . number_format($total_expense, 0, ',', '.'), 1, 1, 'R');
        
        $pdf->Cell(80, 8, 'Total Penghasilan Bersih', 1, 0, 'L', true);
        $pdf->Cell(80, 8, 'Rp ' . number_format($total_net_income, 0, ',', '.'), 1, 1, 'R');
        
        $pdf->Ln(10);
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(52, 152, 219);
        $pdf->SetTextColor(255);
        
        $headers = array_keys($data[0] ?? []);
        $widths = [30, 30, 45, 45, 45, 45];
        
        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($widths[$i], 10, $headers[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Table data
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);
        $fill = false;
        
        foreach($data as $row) {
            $i = 0;
            foreach($row as $value) {
                // Adjust alignment based on column content (right-align for numeric data)
                $align = ($i >= 2) ? 'R' : 'L';
                $pdf->Cell($widths[$i], 8, $value, 1, 0, $align, $fill);
                $i++;
            }
            $pdf->Ln();
            $fill = !$fill;
        }
        
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