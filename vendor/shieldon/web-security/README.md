# Shieldon Web-Security

This package is a part of [Shieldon Firewall 2](https://github.com/terrylinooo/shieldon).

## Installation

Use PHP Composer:

```php
composer require shieldon/web-security
```

Or, download it and include the Shieldon autoloader.
```php
require 'autoload.php';
```

## Usage

#### Clean single variable
```php
$xss = new \Shieldon\Security\Xss();

$_POST['username'] = 'javascript:/*--></title></style></textarea></script></xmp><svg/onload=\'+/"/+/onmouseover=1/+/[*/[]/+alert(1)//\'>';

$username = $xss->clean($_POST['username']);

echo $username;
```

result
```
[removed]/*--&gt;&lt;/title&gt;&lt;/style&gt;&lt;/textarea&gt;[removed]</xmp>&lt;svg/[removed]&gt;
```

#### Clean a superglobal
```php
$xss = new \Shieldon\Security\Xss();

$_GET = $xss->clean($_GET);
```


---

#### Author

- [Terry L.](https://terryl.in)

#### License

MIT
