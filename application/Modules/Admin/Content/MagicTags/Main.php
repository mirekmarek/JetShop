<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\MagicTags;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers_Content_MagicTags;


class Main extends Admin_Managers_Content_MagicTags
{
	
	public function renderTool(): string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$handler = new Handler( $view );
				
				$view->setVar( 'handler', $handler );
				
				return $view->render('tool');
			}
		);
	}
}