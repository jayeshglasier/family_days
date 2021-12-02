<?php 
namespace App\Helper;

use Illuminate\Database\Eloquent\Helper;

/**
* 
*/
class ResponseMessage
{
	public static function success($msg,$data)
	{	
		header('Content-type: application/json');
		echo json_encode(['status' => true, 'error' => 200, 'message' => $msg, 'data' => $data],JSON_UNESCAPED_SLASHES);
	}

	public static function error($msg)
	{
		header('Content-type: application/json');
		echo json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);	
	}

	public static function successMessage($msg)
	{
		header('Content-type: application/json');
		echo json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);	
	}
}
?>