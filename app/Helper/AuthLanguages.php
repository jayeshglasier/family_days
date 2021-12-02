<?php 
namespace App\Helper;

use Illuminate\Database\Eloquent\Helper;
use Illuminate\Http\Request;

class AuthLanguages
{
	public static function multiLanguage(Request $request)
	{	
		$language = $request->language;

		if($language == "english")
        {
            $data['authlanguage'] = [ 	
            	"active_success" => "This account has been activated.",
				"inactive" => "This account has not been activated yet.",
				"token_invalid" => "Token you entered is not valid.",
				"token_required" => "Token is required.",
        	];

        }else if($language == "spanish")
        {
            $data['authlanguage'] = [ 
            	"active_success" => "Esta cuenta ha sido activada.",
            	"inactive" => "Esta cuenta no ha sido activada todavía.",
            	"token_invalid" => "El token que ingresó no es válido..",
            	"token_required" => "Se requiere token.",
            ];

        }else if($language == "franch")
        {
            $data['authlanguage'] = [ 
            	"active_success" => "Ce compte a été activé.",
            	"inactive" => "Ce compte n'a pas encore été activé.",
            	"token_invalid" => "Le jeton que vous avez entré n'est pas valide.",
            	"token_required" => "Le jeton est requis.",
            ];

        }else{
            $data['authlanguage'] = [ 
            	"active_success" => "This account has been activated.",
            	"inactive" => "This account has not been activated yet.",
            	"token_invalid" => "Token you entered is not valid.",
            	"token_required" => "Token is required.",
            ];
        }

        return $data['authlanguage'];
	}

}
?>