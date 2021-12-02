<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'not_id';
    const CREATED_AT = 'not_createdat';
    const UPDATED_AT = 'not_updatedat';
    protected $table = 'tbl_notifications';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}