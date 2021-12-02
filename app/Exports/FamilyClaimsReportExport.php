<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Rewards;
use DB;

class FamilyClaimsReportExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        $lastYears = date("Y") - 4;
        $lastfiveYear = $lastYears."-01-01";
        $currentYears = date("Y") + 4;
        $currentYearDate = $currentYears."-12-31";

        return Rewards::select('users.use_family_name','red_rewards_name','red_brand_name','red_cat_name','red_point','use_full_name','bds_link',DB::raw("DATE_FORMAT(red_is_claim_date, '%b-%d-%Y') as expired_date"))->join('users','tbl_rewards.red_child_id','users.id')
        ->leftjoin('tbl_sub_brands','tbl_rewards.red_brand_icon','tbl_sub_brands.bds_brand_icon')
        ->whereBetween('red_is_claim_date', [$lastfiveYear, $currentYearDate])
        ->where('red_is_claim',1)
        ->orderby('red_is_claim_date','ASC')
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
            'Link',
            'Claim Date'
        ];
    }
}