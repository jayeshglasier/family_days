<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'sys_id';
    const CREATED_AT = 'sys_createdat';
    const UPDATED_AT = 'sys_updatedat';
    protected $table = 'tbl_system_setting';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
