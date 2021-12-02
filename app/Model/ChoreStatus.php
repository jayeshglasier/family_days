<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ChoreStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'cos_id';
    const CREATED_AT = 'cos_createat';
    const UPDATED_AT = 'cos_updateat';
    protected $table = 'tbl_chores_status';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
