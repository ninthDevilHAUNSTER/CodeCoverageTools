# Debloating 工具修改与踩坑

## 一些隐藏的配置信息

修改网站根目录

```php
//config.php
$config['base_url'] = 'http://localhost:8086/admin';
```

修改数据库信息（port需要自己加上去）

```php
//admin/application/config/database.php
$db['default'] = array(
    'dsn' => '',
    'hostname' => 'websec',
    'username' => 'root',
    'password' => 'password',
    'database' => 'code_coverage',
    'dbdriver' => 'mysqli',
    'port' => "22306");
```

存储目录

```php
//admin/application/controllers/Software_file.php
$files = $this->getDirs('D:\wamp\php_box\html');
```
之后考虑把存储目录改写到config.php中去了

## 一些坑

