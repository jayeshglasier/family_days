<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MessagesCounts extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'msc_id';
    const CREATED_AT = 'msc_createat';
    const UPDATED_AT = 'msc_updateat';
    protected $table = 'tbl_message_count';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
