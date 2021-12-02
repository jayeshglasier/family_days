<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PresetReward extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'per_id';
    const CREATED_AT = 'per_createat';
    const UPDATED_AT = 'per_updateat';
    protected $table = 'tbl_preset_reward';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}