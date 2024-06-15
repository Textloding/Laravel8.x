<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

> 这是一个基于laravel8.x和dcatadmin的集成快速开发框架，包含api接口、后台管理等基础通用功能。

### 初始化操作
##### 1.加载依赖 <br  />
`composer update`
##### 2.数据库以及其他配置 <br  />
修改.env文件中的数据库以及其他配置
##### 3.安装Dcatadmin <br  />
`composer require dcat/laravel-admin:"2.*" -vvv`   
##### 4.数据库迁移 <br  />
`php artisan migrate`
##### 5.发布资源 <br  />
`php artisan admin:publish`
##### 6.注入admin <br  />
`php artisan admin:install`
##### 7.注入菜单 <br  />
`php artisan db:seed --class=CustomMenuSeeder`
##### 8.生成appkey（生产环境请执行） <br  />
`php artisan key:generate`
##### 9.生成jwt-secret（生产环境请执行） <br  />
`php artisan jwt:secret`
##### 10.生成路由缓存文件（生产环境请执行，路由更改后请清理缓存后再执行） <br  />
`php artisan cache:route`
##### 11.生成配置缓存文件 （生产环境请执行，.env文件以及配置文件更改后请清理缓存后再执行） <br  />
`php artisan config:cache`
##### 12.生成视图缓存文件（生产环境请执行，视图更改后请清理缓存后再执行） <br  />
`php artisan view:cache`
##### 13.生成事件和监听器缓存文件（生产环境请执行，事件和监视器更改后请清理缓存后再执行） <br  />
`php artisan event:cache`
##### 14.运行项目 <br  />
`php artisan serve`
```
后台入口:http://你的域名/admin 
账号:admin
密码:admin
```
###
[接口文档传送](https://doc.apipost.net/docs/2b46cc936c64000)


### 已完成模块
1. Laravel基础安装
2. Dcatadmin安装
3. Api响应规则统一规范
4. 接入<br  />
**[tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)** <br  />
**[propaganistas/laravel-phone](https://github.com/Propaganistas/Laravel-Phone)** <br  />
**[overtrue/easy-sms](https://github.com/overtrue/easy-sms)** <br  />
**[overtrue/laravel-socialite](https://github.com/overtrue/laravel-socialite)** <br  />
开发后台阿里云短信、第三方登录开关按钮(开关请注意config/api_switch.php权限问题)和配置基础注册登录三方登录api接口
5. 使用curl封装最简访问阿里通义千问模型(api路由正式环境请放入需要登陆的路由组内)
6. 接入[yansongda/pay](https://github.com/yansongda/pay)配置支付宝支付并简单创建页面支付模块（路由在config/web.php）
7. 接口图片上传、后台开关控制接口图片上传、接口上传图片资源管理



### 待完成模块
- 微信支付接入




