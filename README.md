### Bearer Auth

> 环境要求：php8.1 、redis、ext-http、ext-redis、ext-openssl、pecl-http

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

- **前端使用**

记得请求头要这样写：
```js
request.headers['Authorization'] = 'Bearer' + ' ' + getToken() // getToken()是你定义的获取token方法
```

## LICENSE

基于 MIT 开源