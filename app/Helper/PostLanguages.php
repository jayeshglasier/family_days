<?php 
namespace App\Helper;

use Illuminate\Database\Eloquent\Helper;
use Illuminate\Http\Request;

class PostLanguages
{
	public static function multiLanguage(Request $request)
	{	
		$language = $request->language;

		if($language == "english")
        {
            $data['authlanguage'] = [ 	
            	"post_create" => "Your post added succefully.",
        	];

        }else if($language == "spanish")
        {
            $data['authlanguage'] = [ 
            	"post_create" => "Tu publicación se agregó correctamente.",
            ];

        }else if($language == "franch")
        {
            $data['authlanguage'] = [ 
            	"post_create" => "Votre message a été ajouté avec succès.",
            ];

        }else{
            $data['authlanguage'] = [ 
            	"post_create" => "Your post added succefully.",
            ];
        }

        return $data['authlanguage'];
	}

}
?>