<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'brd_id';
    const CREATED_AT = 'brd_createat';
    const UPDATED_AT = 'brd_updateat';
    protected $table = 'tbl_brands';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
