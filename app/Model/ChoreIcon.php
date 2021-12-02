<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ChoreIcon extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'chi_id';
    const CREATED_AT = 'chi_createat';
    const UPDATED_AT = 'chi_updateat';
    protected $table = 'tbl_chore_icon';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
