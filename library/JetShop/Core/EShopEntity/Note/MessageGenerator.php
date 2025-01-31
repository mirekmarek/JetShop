<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\MVC_View;
use JetApplication\AdministratorSignatures;
use JetApplication\EShop;

abstract class Core_EShopEntity_Note_MessageGenerator {
	
	public const KEY = null;
	
	protected MVC_View $view;
	protected EShop $eshop;
	
	public function getKey() : string
	{
		return static::KEY;
	}
	
	abstract public function getTitle() : string;
	
	abstract public function generateSubject() : string;
	
	public function renderSubject() : string
	{
		return trim($this->view->render('subject'));
	}
	
	abstract public function generateText() : string;
	
	public function renderText( bool $append_signature) : string
	{
		$text = $this->view->render('text');
		
		if($append_signature) {
			$text .= "\n\n".AdministratorSignatures::getSignature( $this->eshop );
		}
		
		return $text;
	}

}