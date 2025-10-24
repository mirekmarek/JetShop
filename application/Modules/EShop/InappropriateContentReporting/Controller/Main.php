<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\InappropriateContentReporting;

use Jet\MVC_Controller_Default;
use JetApplication\InappropriateContentReporting;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$report = new InappropriateContentReporting();
		
		$this->view->setVar( 'report', $report);
		
		if($report->processNew()) {
			$report->save();
			
			$this->output('done');
		} else {
			$this->output('default');
		}
		
		
	}
	
	
}