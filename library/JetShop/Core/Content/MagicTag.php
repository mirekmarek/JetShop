<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\MVC_Layout;
use Jet\MVC_Layout_OutputPostprocessor;
use Jet\MVC_View;

abstract class Core_Content_MagicTag extends MVC_Layout_OutputPostprocessor
{
	protected MVC_View $view;
	
	public const ID = null;
	
	public function getId(): string
	{
		return static::ID;
	}
	
	abstract public function getTitle() : string;
	
	abstract public function getDescription() : string;
	
	public function __construct( ?MVC_Layout $layout=null, ?MVC_View $view=null )
	{
		if($layout) {
			parent::__construct( $layout );
		}
		
		if($view) {
			$this->view = $view;
		}
	}
	
	
	abstract public function generate( array $contexts ) : string;
	
	abstract public function getContexts() : array;
	
}