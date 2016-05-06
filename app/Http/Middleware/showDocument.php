<?php

namespace App\Http\Middleware;

use Closure;

class showDocument
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
        if(!empty($request->input('num'))){
            return redirect('/doc/РДС-'.$request->input('num'));
        }
        return  view()->make('main')
            ->nest('main','error_input')
            ->nest('header', 'child.header')
            ->nest('leftsidebar', 'child.leftsidebar')
            ->nest('rightsidebar', 'child.rightsidebar');
    }
}
