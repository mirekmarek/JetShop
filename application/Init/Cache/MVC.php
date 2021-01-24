<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 *
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */
namespace JetShop;

use Jet\Mvc_Cache;
use Jet\SysConf_Path;


use Jet\Mvc_Cache_Backend_Files;
require_once SysConf_Path::getLibrary().'Jet/Mvc/Cache/Backend/Files.php';
$backend = new Mvc_Cache_Backend_Files();


/*
use Jet\Mvc_Cache_Backend_Redis;
require_once SysConf_Path::getLibrary().'Jet/Mvc/Cache/Backend/Redis.php';
$backend = new Mvc_Cache_Backend_Redis();
*/

Mvc_Cache::init( $backend );

