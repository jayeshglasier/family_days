<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RewardsCategorys extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'rec_id';
    const CREATED_AT = 'rec_createat';
    const UPDATED_AT = 'rec_updateat';
    protected $table = 'tbl_rewards_categorys';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
