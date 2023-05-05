<?php
namespace JetApplication;

use Jet\SysConf_Path;
use Jet\SysConf_URI;

Images::setRootPath( SysConf_Path::getBase().'images/' );
Images::setRootUrl( SysConf_URI::getBase().'images/' );
ImagesShared::setRootPath( SysConf_Path::getBase().'images/' );
ImagesShared::setRootUrl( SysConf_URI::getBase().'images/' );
