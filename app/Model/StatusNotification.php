<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StatusNotification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'sno_id';
    const CREATED_AT = 'sno_createat';
    const UPDATED_AT = 'sno_updateat';
    protected $table = 'tbl_status_notifications';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}