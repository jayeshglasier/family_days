<?php
namespace App\Exports;
use App\Model\PresetChores;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PreChoreesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PresetChores::select('pre_title','pre_createat')->where('pre_title','<>','')->get();
    }

    public function headings(): array
    {
        return [
            'Preset Chores',
            'Created Date'
        ];
    }
}