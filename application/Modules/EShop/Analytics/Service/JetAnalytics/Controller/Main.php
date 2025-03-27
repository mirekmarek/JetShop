<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;


class Controller_Main extends Report_Controller
{
	
	public function default_Action() : void
	{
		$this->output('general');
	}

	
	public function getReportsList(): array
	{
		return Report_General::getList();
	}
	
	protected function preinitReport(): void
	{
	}
	
	protected function output( string $view_script ): void
	{
		$output = $this->view->render( $view_script );
		
		$this->content->output( $output );
	}
	
}