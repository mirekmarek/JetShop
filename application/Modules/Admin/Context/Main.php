<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Context;


use Jet\Application_Module;
use Jet\Factory_MVC;
use JetApplication\Admin_Managers_Context;
use JetApplication\Context;


class Main extends Application_Module implements Admin_Managers_Context
{
	
	public function showContext( Context $context ): string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar('context', $context );
		
		return $view->render('context');
	}
}