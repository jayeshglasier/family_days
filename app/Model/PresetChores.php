<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PresetChores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'pre_id';
    const CREATED_AT = 'pre_createat';
    const UPDATED_AT = 'pre_updateat';
    protected $table = 'tbl_preset_chores';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
