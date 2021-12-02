<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    
    protected $primaryKey = 'exc_id';

    const CREATED_AT = 'exc_createat';
    const UPDATED_AT = 'exc_updateat';

    protected $table = "tbl_exception";
}
