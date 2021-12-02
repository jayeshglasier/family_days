<?php 
namespace App\Helper;

use Illuminate\Database\Eloquent\Helper;
use App\Model\Exception;

/**
* 
*/
class Exceptions 
{
	public static function exception($ex)
	{
		$data = ([	
				'exc_error' => substr($ex,1,500),
				'exc_createat' => date('Y-m-d H:i:s'),
				'exc_updateat' => date('Y-m-d H:i:s')
			]);

		$create = Exception::insert($data);

		if($create) {
			echo substr($ex,1,500);
		}
	}
}


?>