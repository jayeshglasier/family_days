<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Rewards;
use DB;

class FamilyRewardExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        $lastYears = date("Y") - 4;
        $lastfiveYear = $lastYears."-01-01";
        $currentYears = date("Y") + 4;
        $currentYearDate = $currentYears."-12-31";

        return Rewards::select('users.use_family_name','red_rewards_name','red_brand_name','red_cat_name','red_point','use_full_name','bds_link',DB::raw("DATE_FORMAT(red_frame_date, '%b-%d-%Y') as expired_date"),DB::raw("DATE_FORMAT(red_createat, '%b-%d-%Y') as created_date"))->join('users','tbl_rewards.red_child_id','users.id')
        ->leftjoin('tbl_sub_brands','tbl_rewards.red_brand_icon','tbl_sub_brands.bds_brand_icon')
        ->whereBetween('red_frame_date', [$lastfiveYear, $currentYearDate])
        ->where('red_status',0)
        ->orderby('red_frame_date','ASC')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Family Name',
            'Reward Name',
            'Brand Name',
            'Category Name',
            'Point',
            'Child Name',
            'Product Link',
            'Expired Date',
            'Created Date'
        ];
    }
}