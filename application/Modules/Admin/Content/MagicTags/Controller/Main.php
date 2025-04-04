<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\MagicTags;

use Jet\MVC_Controller_Default;

class Controller_Main extends MVC_Controller_Default
{

	public function default_Action() : void
	{
		$handler = new Handler($this->view);
		
		
		$this->content->output(
			$handler->handle()
		);
	}
}