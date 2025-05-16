<?php
namespace App\Services;

use TCPDF;
use Carbon\Carbon;

class OperationalPdfService
{
    public function generate($data, $filename, $total_operasional, $total_pengeluaran, $expense_by_type, $month, $year, $truck_info)
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('PT Harry');
        $pdf->SetAuthor('PT Harry');
        $pdf->SetTitle('Laporan Operasional');
        $pdf->SetSubject('Laporan Operasional Truk');
        
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
            $title = "Laporan Operasional - Truk {$truck_info->plate_number} ($category_text)";
        } else {
            $title = "Laporan Operasional - Semua Truk";
        }
        $title .= " - $bulan $year";
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Summary
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(135, 10, 'Total Operasional: ' . $total_operasional . ' Item', 1, 0, 'C', true);
        $pdf->Cell(135, 10, 'Total Pengeluaran: Rp ' . number_format($total_pengeluaran, 0, ',', '.'), 1, 1, 'C', true);
        $pdf->Ln(5);
        
        // Top 5 expense types if available
        if (count($expense_by_type) > 0) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Top 5 Jenis Pengeluaran', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 9);
            foreach ($expense_by_type as $expense) {
                $pdf->Cell(100, 7, $expense['name'], 1, 0, 'L');
                $pdf->Cell(70, 7, 'Rp ' . number_format($expense['total_expense'], 0, ',', '.'), 1, 1, 'R');
            }
            $pdf->Ln(5);
        }
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(52, 152, 219);
        $pdf->SetTextColor(255);
        
        $headers = array_keys($data[0] ?? []);
        $widths = [20, 35, 55, 25, 20, 30, 30];
        
        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($widths[$i], 10, $headers[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Table data
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 8);
        $fill = false;
        $total = 0;
        
        foreach($data as $row) {
            $i = 0;
            foreach($row as $key => $value) {
                $align = ($key == 'Jumlah' || $key == 'Harga' || $key == 'Quantity') ? 'R' : 'L';
                $pdf->Cell($widths[$i], 8, $value, 1, 0, $align, $fill);
                $i++;
            }
            $pdf->Ln();
            $fill = !$fill;
            
            // Extract numeric value for summing
            $jumlah = (int)str_replace(['.', ','], '', $row['Jumlah']);
            $total += $jumlah;
        }
        
        // Add total row
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(array_sum(array_slice($widths, 0, 6)), 8, 'Total Pengeluaran:', 1, 0, 'R', true);
        $pdf->Cell($widths[6], 8, 'Rp ' . number_format($total_pengeluaran, 0, ',', '.'), 1, 0, 'R', true);
        
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