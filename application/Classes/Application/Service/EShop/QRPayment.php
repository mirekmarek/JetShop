<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	multiple_mode: false,
	name: 'QR Payment',
	description: '',
	module_name_prefix: 'Payment.'
)]
interface Application_Service_EShop_QRPayment
{
	public function generateQR( Order $order ) : ?QRCode;
	
	public function checkOrderIsPaid( Order $order ) : void;
	
	public function generateReminderEmailInfoText( Order $order ) : string;
}