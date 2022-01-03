<?php

namespace App\Http\Middleware;
use App\User;
use Validator;
use Closure;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = $request->header('token'); // User token

        if ($header) {
            
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                { 
                    return $next($request);
                }else{
                    $msg = "Your account isn't active.";
                    return response()->json(['success' => false, 'error' => 401, 'message' => $msg]);
                }
            }else{
                $msg = "Token isn't valid!";
                return response()->json(['success' => false, 'error' => 401, 'message' => $msg]);
            }
          
        }else{
            $msg = "Token is required!";
            return response()->json(['success' => false, 'error' => 401, 'message' => $msg]);
        }
        
    }
}
