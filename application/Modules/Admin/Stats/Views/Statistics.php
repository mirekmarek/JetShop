<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Stats\Views;

use Jet\MVC_View;
use JetApplication\Statistics as JetApplication_Statistics;

abstract class Statistics extends JetApplication_Statistics
{
	protected MVC_View $view;
	
	public function getView(): MVC_View
	{
		return $this->view;
	}
	
	public function setView( MVC_View $view ): void
	{
		$this->view = $view;
	}
	
	abstract public function init() : void;
	
	public function setupForm() : string
	{
		return $this->view->render( $this->getKey().'/setup-form' );
	}
	
	public function output() : string
	{
		return $this->view->render( $this->getKey().'/output' );
	}
}