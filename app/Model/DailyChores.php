<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DailyChores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'chd_id';
    const CREATED_AT = 'chd_createdat';
    const UPDATED_AT = 'chd_updatedat';
    protected $table = 'tbl_daily_chores';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}