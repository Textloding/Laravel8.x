<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

> 这是一个基于laravel8.x和dcatadmin的模块化练习项目

### 初始化操作
##### 1.加载依赖 <br  />
`composer update`
##### 2.数据库配置 <br  />
修改.env文件中的数据库配置
##### 3.安装Dcatadmin <br  />
`composer require dcat/laravel-admin:"2.*" -vvv`   
##### 4.数据库迁移 <br  />
`php artisan migrate`
##### 5.发布资源 <br  />
`php artisan admin:publish`
##### 6.注入admin <br  />
`php artisan admin:install`
##### 7.生成appkey（生产环境请执行） <br  />
`php artisan key:generate`
##### 8.生成路由缓存文件（生产环境请执行，路由更改后请清理缓存后再执行） <br  />
`php artisan cache:route`
##### 9.生成配置缓存文件 （生产环境请执行，路由更改后请清理缓存后再执行） <br  />
`php artisan config:cache`
##### 10.生成视图缓存文件（生产环境请执行，路由更改后请清理缓存后再执行） <br  />
`php artisan view:cache`
##### 11.生成事件和监听器缓存文件（生产环境请执行，路由更改后请清理缓存后再执行） <br  />
`php artisan event:cache`
##### 12.运行项目 <br  />
```
后台入口:http://你的域名/admin 
账号:admin
密码:admin
```

### 已完成模块
1. Laravel基础安装
2. Dcatadmin安装



### 待完成模块





