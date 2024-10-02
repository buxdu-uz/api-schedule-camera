<?php

namespace App\Exports;

use App\Domain\Buildings\Models\Building;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class BuildingRoomExport implements FromCollection, WithHeadings,WithMapping, WithEvents
{

    public function collection()
    {
        $buildings = Building::query()
            ->with('rooms')
            ->get(); // Fetch buildings with rooms and eager load

        // Transform data for export
        $exportData = [];

        foreach ($buildings as $building) {
            foreach ($building->rooms as $room) {
                $exportData[] = [
                    'building_id' => $building->id,
                    'building_name' => $building->name,
                    'room_id' => $room->id,
                    'room_name' => $room->name,
                ];
            }
        }

        // Return as a collection
        return collect($exportData);
    }

    public function headings(): array
    {
        return [
            ['Bino ID', 'Bino nomi', 'Xona ID', 'Xona nomi']
        ];
    }

    public function map($row): array
    {
        return [
            $row['building_id'],
            $row['building_name'],
            $row['room_id'],
            $row['room_name'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                // Style header
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Adjust column widths
                foreach (range('A', 'D') as $columnID) {
                    $sheet->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
            },
        ];
    }
}
