<?php

require 'SplClassLoader.php';

$autoLoader = new SplClassLoader('Proxy', dirname(__FILE__));
$autoLoader->register();