<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminContact extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'amc_id';
    const CREATED_AT = 'amc_createat';
    const UPDATED_AT = 'amc_updateat';
    protected $table = 'tbl_admin_contact';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
