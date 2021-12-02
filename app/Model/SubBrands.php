<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubBrands extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'bds_id';
    const CREATED_AT = 'bds_createat';
    const UPDATED_AT = 'bds_updateat';
    protected $table = 'tbl_sub_brands';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
