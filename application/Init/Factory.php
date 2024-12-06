<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication;

//Debug_Profiler::blockStart('INIT - ClassNames');
use Jet\Factory_MVC;

/**
 * Example:
 *
 * use Jet\Factory_Application;
 *
 * Factory_Application::setModuleManifestClassName( Application_Module_Manifest::class );
 */

Factory_MVC::setPageContentClassName( MVC_Page_Content::class );
Factory_MVC::setPageClassName( MVC_Page::class );

//Debug_Profiler::blockEnd('INIT - ClassNames');
