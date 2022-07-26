### Bearer Auth

> 环境要求：php8.1 、redis

### 架构图

![image](yuque.jpg)

### 请安装以下扩展

- redis
- pecl-http

### 引入包

```shell
composer require xgbnl/laravel-guard
```

### 发布配置文件

```shell
php artisan guard:install
```

### 简单使用

1. 编辑 `.env`，配置 `redis`

```dotenv
REDIS_HOST=redis
REDIS_PASSWORD=123456
REDIS_PORT=6379
```
如果你希望把`token`存储到其它 `DB`，而不是默认的 `DB0`,请配置`config/bearer.php`
```php
 'redis' => [
            'connect' => 'default', //　这里配置你的新连接，默认为 db0
        ],
```

2. 配置路由中间件 `app/Http/Kernel.php` 

```php
use Xgbnl\Bearer\Middleware\BearerAuthorization;

protected $routeMiddleware = [
    // ....
    'guard' => BearerAuthorization::class,
];

 ```

3. 在`api`路由里使用

```php 
Route::middleware('guard:user')->get('/user',fn() => 'ok');
```

### 详细使用

**为确保功能正常使用，`bearer.php` 配置文件中的提供者模型需要实现 `Xgbnl\Bearer\Contracts\Authenticatable` 接口,这里提供的 `trait`
已经实现了该接口的方法(如果你不喜欢我为你准备的`trait`,你可以自己在模型里实现这些方法)，那么你应该像下面这样配置你的模型:**

```php
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Traits\AuthenticatableHelpers;

class User implements Authenticatable
{
    use AuthenticatableHelpers;
}
```

- **前端使用**

记得请求头要这样写：
```js
request.headers['Authorization'] = 'Bearer' + ' ' + getToken() // getToken()是你定义的获取token方法
```

- **Laravel中使用**

> guard 辅助函数会返回一个守卫实例，`login` 函数实现了为用户具体存储`token`的逻辑，你只需要知道它返回一个数组

```php
public function login()
{
    // Get input parameter
    $validated = $this->validate();

    // Get user
    $user = User::query()->where('email',$validated['email'])->first();

    // Validate ....

    // Auto login
    $tokens = guard('user')->login($user); 

    /*
    $tokens 变量内容
    [
        `access_token` =>"0I3EwWrg5CNSJx84hclUx2Ok......"
        `token_type`   => "Bearer"
    ]
    */
    
    // Get user permission
    $permission = $user->permission;

    return json(['permission' => $permission,'guard'=>$tokens]);
}

```

- **使用注销**

> 使用前提，用户必须为登录的情况下，否则抛出异常

```php
guard('user')->logout();
```

## 扩展中间件

> 一旦涉及到隐私、金钱、密码之类的操作，必须要再次验证身份。包里提供两种保护方式，根据实际业务去调整

- 通过校验`ip`地址
- 通过校验设备信息

**创建一个`FilterMiddleware`**

```shell
php artisan make:middleware FilterMiddleware
```

**继承 `Xgbnl\Bearer\Middleware\Authorization`,并实现 `doHandle()方法`**

```php

use Xgbnl\Bearer\Middleware\Authorization;

class FilterMiddleware extends Authorization
{
    public function doHandle(){
    
    // TODO: Implement doHandle() method.
        
        // 本次访问IP与上次记录IP不一致时
        if ($this->guard()->validateClientIP()){
        
            // .... 进行设备验证逻辑 
            
            // 或者抛出异常
        }
        
        // 本次访问的设备与上次不一致时
        if ($this->guard()->validateDevice()){
            // 验证操作或抛出异常
        }
    }
}
```
### 过期时间（expires in）

> 目前默认是2天，因为这个项目是从我的一个OA项目抽出来的， `redis` 自身就能设定过期值。所以对这一块没怎么关注，后面会慢慢拓展这个小工具。

### 写在最后

JWT没有更新了，所以索性写了一个这个包，毕竟还要靠`php`养家糊口的，如果这个项目对你有帮助，不要吝啬你的鼠标，帮我`star`。毕竟，写代码是要秃头的

## LICENSE

基于 MIT 开源