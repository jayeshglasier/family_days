<?php
namespace App\Exports;
use App\Model\PresetReward;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PreRewardNameExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PresetReward::select('per_name','per_createat')->where('per_name','<>','')->get();
    }

    public function headings(): array
    {
        return [
            'Reward Name',
            'Created Date'
        ];
    }
}