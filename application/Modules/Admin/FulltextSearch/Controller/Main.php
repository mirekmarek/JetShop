<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\FulltextSearch;


use Jet\Application;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$this->output('default');
	}
	
	
	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();
		
		$result = Index::search(
			entity_type: $GET->getString('class'),
			search_string: $GET->getString('whisper'),
			object_type_filter:  $GET->exists('type') ? explode(',', $GET->getString('type')) : null,
			object_is_active_filter: $GET->exists('active') ? $GET->getBool('active') : null
		);
		
		
		$this->view->setVar('result', $result);
		echo $this->view->render('whisperer/result');
		
		Application::end();
	}
	
}