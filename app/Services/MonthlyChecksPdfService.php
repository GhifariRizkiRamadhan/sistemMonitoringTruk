<?php
namespace App\Services;

use TCPDF;
use Carbon\Carbon;

class MonthlyChecksPdfService
{
    public function generate($data, $filename, $condition_stats, $high_km_trucks, $low_service_km_trucks, $month, $year, $truck_info)
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('PT Harry');
        $pdf->SetAuthor('PT Harry');
        $pdf->SetTitle('Laporan Pengecekan Bulanan');
        $pdf->SetSubject('Laporan Pengecekan Bulanan Truk');
        
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
            $title = "Laporan Pengecekan Bulanan - Truk {$truck_info->plate_number} ($category_text)";
        } else {
            $title = "Laporan Pengecekan Bulanan - Semua Truk";
        }
        $title .= " - $bulan $year";
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Truck condition summary
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 10, 'Ringkasan Kondisi Truk', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 9);
        
        // Create a table for condition summary
        $conditionTypes = [
            'Ban' => ['good' => 'good_tire', 'fair' => 'fair_tire', 'bad' => 'bad_tire'],
            'Rem' => ['good' => 'good_brake', 'fair' => 'fair_brake', 'bad' => 'bad_brake'],
            'Kabin' => ['good' => 'good_cabin', 'fair' => 'fair_cabin', 'bad' => 'bad_cabin'],
            'Bak' => ['good' => 'good_cargo', 'fair' => 'fair_cargo', 'bad' => 'bad_cargo'],
            'Lampu' => ['good' => 'good_lights', 'fair' => 'fair_lights', 'bad' => 'bad_lights']
        ];
        
        // Headers for the condition table
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(50, 7, 'Komponen', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Baik', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Kurang', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Perlu Perbaikan', 1, 1, 'C', true);
        
        // Fill data for the condition table
        foreach ($conditionTypes as $component => $fields) {
            $pdf->Cell(50, 7, $component, 1, 0, 'L');
            $pdf->Cell(40, 7, $condition_stats->{$fields['good']} ?? 0, 1, 0, 'C');
            $pdf->Cell(40, 7, $condition_stats->{$fields['fair']} ?? 0, 1, 0, 'C');
            $pdf->Cell(40, 7, $condition_stats->{$fields['bad']} ?? 0, 1, 1, 'C');
        }
        
        $pdf->Ln(5);
        
        // High KM trucks section
        if (count($high_km_trucks) > 0) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Truk dengan KM > 50,000', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, 7, 'No. Plat', 1, 0, 'C', true);
            $pdf->Cell(70, 7, 'KM Saat Ini', 1, 1, 'C', true);
            
            foreach ($high_km_trucks as $truck) {
                $pdf->Cell(70, 7, $truck->plate_number, 1, 0, 'L');
                $pdf->Cell(70, 7, number_format($truck->current_km) . ' KM', 1, 1, 'R');
            }
            
            $pdf->Ln(5);
        }
        
        // Low service KM trucks section
        if (count($low_service_km_trucks) > 0) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 10, 'Truk Perlu Servis Segera (Sisa KM < 1,000)', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, 7, 'No. Plat', 1, 0, 'C', true);
            $pdf->Cell(70, 7, 'Sisa KM Sebelum Servis', 1, 1, 'C', true);
            
            foreach ($low_service_km_trucks as $truck) {
                $pdf->Cell(70, 7, $truck->plate_number, 1, 0, 'L');
                $pdf->Cell(70, 7, number_format($truck->service_km_remaining) . ' KM', 1, 1, 'R');
            }
            
            $pdf->Ln(5);
        }
        
        // Main table
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 10, 'Detail Pengecekan Bulanan', 0, 1, 'L');
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(52, 152, 219);
        $pdf->SetTextColor(255);
        
        $headers = array_keys($data[0] ?? []);
        $widths = [20, 25, 25, 25, 25, 25, 25, 25, 25, 50];
        
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
            $currentHeight = 0;
            
            // First, determine the maximum height needed for this row
            foreach($row as $value) {
                $cellHeight = $pdf->getStringHeight($widths[$i], $value, false, true, '', 1);
                if ($cellHeight > $currentHeight) {
                    $currentHeight = $cellHeight;
                }
                $i++;
            }
            
            // Set minimum height
            if ($currentHeight < 8) {
                $currentHeight = 8;
            }
            
            // Now draw each cell with the same height
            $i = 0;
            foreach($row as $value) {
                // Adjust alignment based on column type
                $align = 'L';
                if ($i == 3 || $i == 4) { // For numeric columns like KM
                    $align = 'R';
                }
                
                $pdf->MultiCell($widths[$i], $currentHeight, $value, 1, $align, $fill, 0);
                $i++;
            }
            
            $pdf->Ln($currentHeight);
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