<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::select('use_family_name','use_username','use_full_name','email','use_phone_no','rol_name','created_at')->join('tbl_roles','users.use_role','tbl_roles.rol_id')->get();
    }

    public function headings(): array
    {
        return [
            'Family Name',
            'Username',
            'Fullname',
            'Email Id',
            'Phone Number',
            'User Type',
            'Created Date'
        ];
    }
}