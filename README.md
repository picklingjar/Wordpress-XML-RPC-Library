Wordpress XML-RPC Library by The Pickling Jar Ltd
=================================================

An ISC licensed PHP library for interacting with Wordpress with XML RPC.

Requires
--------

http://phpxmlrpc.sourceforge.net/

Example
-------
```
<?php
require('xmlrpc.inc');
require('Wordpress-XML-RPC-Library/get-pages.php');

$globalerr = null;

$xmlrpcurl = 'http://example.com/xmlrpc.php';
$username = 'admin';
$password = 'password';

$pages = wordpress_get_pages($xmlrpcurl, $username, $password);
if($pages == false){
    echo $globalerr."\n";
    die();
}
else {
    print_r($pages);   
}
?>
```

Proxies
-------
Proxy format is username:password@ipaddress:port

Contributing
------------

Patches & forks most welcome to complete the library and fix any bugs
