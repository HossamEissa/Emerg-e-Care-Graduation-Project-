<?php

namespace App\Http\Middleware;

use App\Traits\responseTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use responseTrait;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
   public function unauthenticated($request, array $guards)
   {
       try {
       $msg = "You are Unauthenticated , Please login ";
       $error = $this->getErrorCode('login');
       return $this->returnError($error , $msg);
       }catch (\Exception $e){
           return response()->json($e->getMessage());
       }
   }
}
