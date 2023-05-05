<?php
/**
 *
 */

namespace JetShop;

use Jet\MVC_View;

use JetApplication\Order_Notification;

abstract class Core_Order_Notification_SMS extends Order_Notification {

	protected string $to_number = '';

	protected string $text_view_script = 'text';


	public function getToNumber() : string
	{
		return $this->to_number;
	}

	public function setToNumber( string $to_number ) : void
	{
		$this->to_number = $to_number;
	}


	public function generateText() : string
	{

		$view = new MVC_View( $this->getViewRootDir() );

		$view->setVar( 'SMS', $this );

		foreach( $this->getViewData() as $k=>$v ) {
			$view->setVar( $k, $v );
		}

		$text = $view->render( $this->getTextViewScript() );

		return $text;
	}


	public function send() : void
	{
		//TODO:
		/*
		SMS::send(
			$this->shop,
			$this->kind,
			$this->generateText(),
			$this->to_number,
			$this->customer_id,
			$this->order_id
		);
		*/

		//TODO: sent
	}


}