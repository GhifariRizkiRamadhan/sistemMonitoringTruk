<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeReportExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $filename;

    public function __construct($data, $filename)
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No. Plat',
            'Muatan',
            'Tanggal Uang Jalan',
            'Tanggal Muat',
            'Tanggal Bongkar',
            'Tonase',
            'Upah/Ton',
            'Ongkos Angkut',
            'Uang Jalan',
            'Hasil Truk'
        ];
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}