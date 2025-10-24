<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Complaints;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Application_Service_EShop_Complaint;
use JetApplication\Complaint;
use JetApplication\Complaint_Image;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShop_Pages;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Item_SetItem;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Service_EShop_Complaint implements
	EShop_ModuleUsingTemplate_Interface,
	SysServices_Provider_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	EMail_TemplateProvider,
	Admin_ControlCentre_Module_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	public function generateCreateURL( Order $order, Order_Item|Order_Item_SetItem $order_item ): false|string
	{
		if( !$order_item->isPhysicalProduct() ) {
			return false;
		}
		
		return EShop_Pages::Complaints( $order->getEshop() )->getURL(
			GET_params: [
				'order' => $order->getKey(),
				'product_id' => $order_item->getItemId(),
				'm' => sha1($order->getEmail())
			]
		);
	}
	
	public function getSysServicesDefinitions(): array
	{
		$new_images_notification = new SysServices_Definition(
			module: $this,
			name: 'Complaints - new image notificator',
			description: '',
			service_code: 'new_image_notificator',
			service: function() {
				$this->sendNewImageNotification();
			}
		);
		
		return [$new_images_notification];
	}
	
	public function sendNewImageNotification(): void
	{
		$images = Complaint_Image::dataFetchAll(
			select: [
				'id',
				'complaint_id',
			],
			where: [
				'notification_sent' => false,
			]
		);
		
		$map = [];
		foreach($images as $image) {
			$complaint_id = $image['complaint_id'];
			$image_id = $image['id'];
			
			$map[$complaint_id][] = $image_id;
		}
		
		foreach($map as $complaint_id => $image_ids) {
			$complaint = Complaint::get( $complaint_id );
			$images = [];
			echo "{$complaint->getNumber()}\n";
			foreach($complaint->getImages() as $image) {
				if( in_array( $image->getId(), $image_ids ) ) {
					echo "\t{$image->getName()}\n";
					$images[] = $image;
				}
			}
			
			$template = new EMailTemplate_NewImageNotification();
			$template->setComplaint($complaint);
			$template->setImages($images);
			$email = $template->createEmail( $complaint->getEshop() );
			
			
			/**
			 * @var Config_PerShop $complaint
			 */
			$cfg = $this->getEshopConfig( $complaint->getEshop() );
			
			$to = $cfg->getSendNotificationsTo();

			$email->setTo( $to );
			$email->send();
			
			foreach($images as $image) {
				Complaint_Image::updateData(data: [
					'notification_sent' => true,
				], where: [
					'id' => $image->getId(),
				]);
				
			}
			
			
		}
		
		
	}
	
	public function getEMailTemplates(): array
	{
		return [
			new EMailTemplate_NewImageNotification()
		];
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Complaints';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'triangle-exclamation';
	}
	
	public function getControlCentrePriority(): int
	{
		return 999;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
}