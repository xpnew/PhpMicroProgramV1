<?php
namespace app\Test\controller;  
use Redis;

class TestRedis
{   
    public function index()
    {   
        $key = 'test_redis_key';
        $value = 'Hello, Redis!';

        // 存储数据到 Redis
        \think\facade\Cache::set($key, $value, 3600); // 1小时过期

        // 从 Redis 获取数据
        $storedValue = \think\facade\Cache::get($key);

        return json([
            'stored_value' => $storedValue
        ]);
    }



    public function test()
    {
    
        $redis =  new  Redis();
        $redis -> connect('127.0.0.1', 6379);
        $redis -> auth('redisadmin'); // 如果有密码，取消注释并设置密码
        $redis -> set('test_key', 'Hello, Redis!');
        $redis -> expire('test_key', 330);
        $redis -> set('test_key2', 'Hello, Redis!', 330);


        $redis -> sadd('WebCache:TestSet',"Test1", 'Hello, Redis!');
        $redis -> expire('WebCache:TestSet', 330);
        $redis -> hset('WebCache:TestHash',"Field1","Value1");
        // $redis -> hset('WebCache:TestHash',"Field2","Value2", 330);
        $redis -> hset('WebCache:TestHash',"Field2","Value2");
        // $redis -> expire('WebCache:TestHash',"Field2", 330);

        $redis -> hset('yxjk:Server:Setting','MakerLevel1Require',1);

        $value = $redis -> get('test_key');

        echo $value;
        return json([
            'value' => $value
        ]);
        
        



    }
    public function test2()
    {
    
       $mng =  \app\comm\SysSetCacheMng::getIns();
       $mng -> Set('Test222',2);

      
       return 'TestRedis test2  ok';

    }
    public function Reload()
    {
    
       $mng =  \app\comm\SysSetCacheMng::getIns();
       $mng -> Reload();

      
       return 'TestRedis Reload  ok';

    }


}







?>