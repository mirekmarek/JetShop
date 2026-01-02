<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\FulltextDictionary;


use Jet\AJAX;
use Jet\Http_Request;

use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\FulltextSearch_Dictionary;


class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$GET = Http_Request::GET();
		$POST = Http_Request::POST();
		
		$this->view->setVar('selected_eshop', $this->getEshop());
		
		switch($GET->getString('action')) {
			case 'add':
				$note = $POST->getString('note');
				$words = $POST->getString('words');
				
				$rec = new FulltextSearch_Dictionary();
				$rec->setNote( $note );
				$rec->setWords( $words );
				$rec->setLocale( $this->getEshop()->getLocale() );
				$rec->save();
				
				
				AJAX::operationResponse(true, data: $rec->jsonSerialize() );
				
				break;
			case 'save':
				$id = $POST->getString('id');
				$note = $POST->getString('note');
				$words = $POST->getString('words');
				
				$rec = FulltextSearch_Dictionary::load( $id );
				$rec->setNote( $note );
				$rec->setWords( $words );
				$rec->save();
				
				AJAX::operationResponse(true, data: $rec->jsonSerialize() );
				
				break;
			case 'delete':
				$id = $POST->getString('id');
				
				$rec = FulltextSearch_Dictionary::load( $id );
				$rec->delete();
				
				AJAX::operationResponse(true);
				break;
		}
		
		
		$this->output('default');
	}
}