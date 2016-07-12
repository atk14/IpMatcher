IpMatcher
=========

Small class for comparing IP addresses.
One address can be compared with a single IP address or array of IPs or subnets.
Works with both IPv4 and IPv6 formats.

Basic usage
-----------

Compare two IPs
```php
IP::Match("127.0.0.1", "127.0.0.1");
```

Check an IP belongs to a 127.0.0.1-127.0.0.255 subnet
```php
IP::Match("127.0.0.1", "127.0.0.1/24");
```
Check an IP matches at least one of addresses/subnets specified in an array
```php
IP::Match("127.0.0.1", array("127.0.0.2", "127.0.0.1/24"));
```


Installation
------------

Just use the Composer:

```
$ cd path/to/your/atk14/project/
$ php composer.phar require atk14/ip-matcher dev-master
```
