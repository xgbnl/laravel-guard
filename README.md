### Bearer Auth

![image](yuque.png)

> 此项目基于 `Laravel` 开发,参考了内置的 guard 实现了一套自定义的守卫器
>
> 守卫器基于 `redis` 存储，不再依赖数据库，使用简单
>
> 已实现了中间件过滤拦截，登录，注销功能

#### 使用

1.引入包
```shell
composer require xgbnl/bearer-auth
```

2. 发布
```shell
php artisan bearer:install
```

3.添加中间件,`app/Http/Kernel.php`
```php
use Xgbnl\Bearer\Middleware\BearerAuthorization;

     protected $routeMiddleware = [
       // ....
        'guard'            => BearerAuthorization::class,
    ];

 ```
4.路由使用
```php 
Route::middleware('guard:user')->get('/test',fn() => 'ok');
```

更多配置查看 `config/bearer.php` , 守卫器角色可以配置多个，这也就是为什么开发这个包的原因：
当你的`User`类和`Employee`都需要 token 守卫验证令牌时，但是内置的 guard 无法动态的转换提
供者，所以干脆自己写了这个包
