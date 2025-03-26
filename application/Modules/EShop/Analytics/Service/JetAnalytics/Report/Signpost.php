<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use JetApplication\Signpost;

abstract class Report_Signpost extends Report
{
	
	protected Signpost $signpost;
	
	public function getSignpost(): Signpost
	{
		return $this->signpost;
	}
	
	public function setSignpost( Signpost $signpost ): void
	{
		$this->signpost = $signpost;
	}
	
	public function init( Report_Controller|Controller_Signpost $controller ) : void
	{
		parent::init( $controller );
		$this->view->setVar( 'signpost', $this->signpost );
	}
	
}