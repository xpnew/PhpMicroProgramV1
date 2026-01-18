<?php

namespace app\http\middleware;

class AdminAuth
{
    public function handle($request, \Closure $next)
    {
        
        if (!session('AdminUser')) {
            return redirect('/admin/index/login');
        }
        return $next($request);
    }
}
