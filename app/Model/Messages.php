<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'cha_id';
    const CREATED_AT = 'cha_createat';
    const UPDATED_AT = 'cha_updateat';
    protected $table = 'tbl_family_message';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
