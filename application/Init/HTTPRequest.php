<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 *
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */
namespace JetShop;

use Jet\Http_Request;
use Jet\SysConf_Jet;

Http_Request::initialize( SysConf_Jet::isHideHttpRequest() );
