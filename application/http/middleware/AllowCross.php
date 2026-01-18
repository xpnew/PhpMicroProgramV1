<?php

namespace app\http\middleware;


use Closure;

use think\Config;

use think\Request;

use think\Response;
use think\facade\Log;


/**

 * 跨域请求支持

 */
class AllowCross
{
    protected $cookieDomain;  

    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Token, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With',
    ];
  
    public function __construct(Config $config)
    {
        $this->cookieDomain = $config->get('cookie.domain', '');
    }


  /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array   $header
     * @return Response
     */
    public function handle($request, Closure $next, ? array $header = [])
    {
        
        Log::record('AllowCross 日志输出 进入中间件 ：' . ' header=' . json_encode($header)  );
        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;
  
        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');
  
            if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {
                $header['Access-Control-Allow-Origin'] = $origin;
            } else {
                $header['Access-Control-Allow-Origin'] = '*';
            }
        }  


        Log::record('AllowCross 日志输出 离开 中间件： ' . ' header=' . json_encode($header)  );
        return $next($request)->header($header);
    }
}