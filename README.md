# Flarum AES Login
 

## Installation

Install with composer:

```sh
composer require thefunpower/flarum-ext-api-login:"*"
```

## Updating

```sh
composer update thefunpower/flarum-ext-api-login:"*"
php flarum migrate
php flarum cache:clear
```

## Create Aes Login Data

`AesEncode` function

~~~   
function flarum_aes_encode($data = [],$key,$iv){
    $data = json_encode($data);
    return @base64_encode(openssl_encrypt($data, 'AES-128-CBC', $key, 1, $iv));
}
~~~

Login 

~~~
$flarum_url = 'http://127.0.0.1:5000';
$data = [
    'nid'=>'third_user_id_1001', 
    'name'=>'admin',
    'tag' =>'rand',
    'created_at'=>time(),
];
$data  = flarum_aes_encode($data);
$token = urlencode(base64_encode($data));
$url   = $flarum_url.'/api/v2/login-token?token='.$token;
~~~

`nid` || `name` must has one

using `$url` in your iframe.

## Register User Account

~~~
$flarum_url = 'http://127.0.0.1:5000';
$data = [
    'nid'=>'third_user_id_1001',
    'name'=>'admin',
    'email'=>'admin@example.com',
    'tag' =>'rand',
    'created_at'=>time(),
];
$data  = flarum_aes_encode($data);
$token = urlencode(base64_encode($data));
$url   = $flarum_url.'/api/v2/login-token?token='.$token;  
~~~

`nid` is unique on third platform

## Links

- [Packagist](https://packagist.org/packages/thefunpower/flarum-ext-api-login)
- [GitHub](https://github.com/thefunpower/flarum-ext-api-login)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)
