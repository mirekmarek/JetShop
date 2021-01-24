<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 *
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */
namespace JetShop;

use Jet\Autoloader_Cache;
use Jet\Autoloader_Cache_Backend_Files;
use Jet\SysConf_Path;

require_once SysConf_Path::getLibrary().'Jet/Autoloader/Cache/Backend/Files.php';

Autoloader_Cache::init( new Autoloader_Cache_Backend_Files()  );
