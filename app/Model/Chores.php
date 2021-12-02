<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Chores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'cho_id';
    const CREATED_AT = 'cho_createat';
    const UPDATED_AT = 'cho_updateat';
    protected $table = 'tbl_chores_list';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
