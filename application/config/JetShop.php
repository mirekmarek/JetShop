<?php
use Jet\Application_Modules;
use Jet\SysConf_Path;
use Jet\SysConf_URI;
use JetShop\Images;

Application_Modules::setModuleRootNamespace('JetShopModule');
Images::setRootPath( SysConf_Path::getBase().'images/' );
Images::setRootUrl( SysConf_URI::getBase().'images/' );
