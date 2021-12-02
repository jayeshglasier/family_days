<?php 
namespace App\Helper;

use Illuminate\Database\Eloquent\Helper;
/**
* 
*/
class NotificationKey 
{
	public static function notificationCurl($fields)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = json_encode ( $fields );
		$headers = array (
		      'Authorization: key=' . "AAAAOdYNOvc:APA91bExCJkdjntcgr7u1Vztw6sRphQn4kzGMYFiTLTEknzkgrlVanwi8Sacl4vqbOumof5tbvHWFhpc7kHrjUniwNzwnx640_7lnS2culjTlixZviCE2wr9EmMmm9-Aq7q_yciEK1J4",
		      'Content-Type: application/json'
		  );

		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
	}

	public static function notificationType()
	{
		$data = [
			"choreByParentType" => "chore_create_by_parent", // Chores created by parent
			"choreByParentContent" => "Chore created for",
			"choreByChildType" => "chore_create_by_child", // Chores created by child
			"choreByChildContent" => "Chore created by",
			"rewardByParentType" => "reward_create_by_parent", // Reward created by parent
			"rewardByParentContent" => "Reward created for",
			"rewardByChildType" => "reward_create_by_child", // Reward created by child
			"rewardByChildContent" => "Reward created by",
			"cliamchildType" => "cliam_create_child", // --- For Cliam by child
			"cliamchildContent" => "Reward claimed by",
			"sendMessageType" => "send_message",  // ---For Send Message
			"sendMessageContent" => "has sent a new message!"
		];

		return $data;
	} 
}


?>