<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\MVC_Controller_Default;
use JetApplication\EShop;

abstract class Core_Admin_ControlCentre_Module_Controller extends MVC_Controller_Default
{
	protected string $output = '';
	
	public function getEshop() : ?EShop
	{
		return $this->getContent()->getParameter('eshop');
	}
	
	public function output( string $view_script ): void
	{
		$this->output .= $this->view->render( $view_script );
		
		$this->content->setOutput( $this->output );
	}
	
}