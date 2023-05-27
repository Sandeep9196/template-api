<?php

namespace App\Http\Middleware;

use Closure;
use Error;
use Illuminate\Http\Request;

class LanguageCurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (request()->segment(3) !== 'upload') {
               if(!$request->lang_id){
                return response()->json([
                    "message" => trans('message.Language Id Required')
                ],401);
            }
            if(!isset($request->cur_id)){
                $request->cur_id = 1;
            }
        }
        return $next($request);
    }
}
