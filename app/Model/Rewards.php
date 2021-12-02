<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rewards extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'red_id';
    const CREATED_AT = 'red_createat';
    const UPDATED_AT = 'red_updateat';
    protected $table = 'tbl_rewards';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
