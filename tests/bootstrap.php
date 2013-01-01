<?php

require 'SplClassLoader.php';

$autoLoader = new SplClassLoader('PhpProxyBuilder', dirname(__FILE__).'/../src');
$autoLoader->register();