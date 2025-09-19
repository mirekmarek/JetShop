<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\BaseObject;
use Jet\Factory_MVC;
use Jet\MVC_Layout;
use Jet\MVC_Page_Content_Interface;
use JetApplication\EShop;
use JetApplication\Application_Service_EShop;
use JetApplication\MVC_Page_Content;

abstract class Core_EShop_PageDefinition_ContentDefinition extends BaseObject {
	protected string $module_name = '';
	protected string $manager_group = Application_Service_EShop::class;
	protected string $manager_interface = '';
	

	protected string $controller_name = 'Main';
	protected string $controller_action = 'default';
	protected array $parameters = [];
	protected bool $is_cacheable = false;
	protected string $output_position = MVC_Layout::DEFAULT_OUTPUT_POSITION;
	protected int $output_position_order = 1;
	
	public function getModuleName(): string
	{
		return $this->module_name;
	}
	
	public function setModuleName( string $module_name ): void
	{
		$this->module_name = $module_name;
	}
	
	public function getManagerGroup(): string
	{
		return $this->manager_group;
	}
	
	public function setManagerGroup( string $manager_group ): void
	{
		$this->manager_group = $manager_group;
	}
	
	
	
	public function getManagerInterface(): string
	{
		return $this->manager_interface;
	}
	
	public function setManagerInterface( string $manager_interface ): void
	{
		$this->manager_interface = $manager_interface;
	}
	
	public function getControllerName(): string
	{
		return $this->controller_name;
	}
	
	public function setControllerName( string $controller_name ): void
	{
		$this->controller_name = $controller_name;
	}
	
	public function getControllerAction(): string
	{
		return $this->controller_action;
	}
	
	public function setControllerAction( string $controller_action ): void
	{
		$this->controller_action = $controller_action;
	}
	
	public function getParameters(): array
	{
		return $this->parameters;
	}
	
	public function setParameters( array $parameters ): void
	{
		$this->parameters = $parameters;
	}
	
	public function isIsCacheable(): bool
	{
		return $this->is_cacheable;
	}
	
	public function setIsCacheable( bool $is_cacheable ): void
	{
		$this->is_cacheable = $is_cacheable;
	}
	
	public function getOutputPosition(): string
	{
		return $this->output_position;
	}
	
	public function setOutputPosition( string $output_position ): void
	{
		$this->output_position = $output_position;
	}
	
	public function getOutputPositionOrder(): int
	{
		return $this->output_position_order;
	}
	
	public function setOutputPositionOrder( int $output_position_order ): void
	{
		$this->output_position_order = $output_position_order;
	}
	
	
	public function getContentModuleName( EShop $eshop ) : string
	{
		if($this->manager_interface) {
			return Application_Service_EShop::list($eshop)->getServiceMetaInfo( $this->manager_interface )?->getName()??'';
		}
		
		return $this->module_name;
	}
	
	public function createPageContentDefinition( EShop $eshop ) : MVC_Page_Content_Interface
	{
		/**
		 * @var MVC_Page_Content $content
		 */
		$content = Factory_MVC::getPageContentInstance();
		
		$content->setOutputPosition( $this->output_position );
		$content->setOutputPositionOrder( $this->output_position_order );
		
		$content->setManagerGroup( $this->getManagerGroup() );
		$content->setManagerInterface( $this->getManagerInterface() );
		$content->setModuleName( $this->getContentModuleName( $eshop ) );
		
		$content->setControllerName( $this->controller_name );
		$content->setControllerAction( $this->controller_action );
		
		$content->setIsCacheable( $this->is_cacheable );
		
		return $content;
	}
	
	
	public static function fromArray( array $data ) : static
	{
		$i = new static();
		
		if(!empty($data['module_name'])) {
			$i->setModuleName( $data['module_name'] );
		}
		if(!empty($data['manager_group'])) {
			$i->setManagerGroup( $data['manager_group'] );
		}
		if(!empty($data['manager_interface'])) {
			$i->setManagerInterface( $data['manager_interface'] );
		}
		if(!empty($data['controller_name'])) {
			$i->setControllerName( $data['controller_name'] );
		}
		if(!empty($data['controller_action'])) {
			$i->setControllerAction( $data['controller_action'] );
		}
		if(!empty($data['parameters'])) {
			$i->setParameters( $data['parameters'] );
		}
		if(!empty($data['is_cacheable'])) {
			$i->setIsCacheable( $data['is_cacheable'] );
		}
		if(!empty($data['output_position'])) {
			$i->setOutputPosition( $data['output_position'] );
		}
		if(!empty($data['output_position_order'])) {
			$i->setOutputPositionOrder( $data['output_position_order'] );
		}
		
		
		return $i;
	}
	
	public function toArray(): array
	{
		return [
			'module_name' => $this->module_name,
			
			'manager_group' => $this->manager_group,
			'manager_interface' => $this->manager_interface,
			
			'controller_name' => $this->controller_name,
			'controller_action' => $this->controller_action,
			
			'parameters' => $this->parameters,
			'is_cacheable' => $this->is_cacheable,
			
			'output_position' => $this->output_position,
			'output_position_order' => $this->output_position_order,
		];
	}
	
}