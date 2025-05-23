<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Attributes;
use Jet\BaseObject;

use Attribute;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_Definition_Property;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
abstract class Core_EShopEntity_Definition extends BaseObject
{
	protected ?string $admin_manager_interface = null;
	protected ?string $entity_name_readable = null;
	protected ?string $entity_type = null;
	protected ?string $URL_template = null;
	protected ?array $images = null;
	protected ?bool $description_mode = null;
	protected ?bool $separate_tab_form_shop_data = null;
	
	/**
	 * @var EShopEntity_Definition_Property[]
	 */
	protected array $properties = [];
	
	protected static array $definitions = [];
	
	public function __construct( ...$attributes )
	{
	}
	
	protected static function read( ReflectionClass $reflection, array $attributes ) : static
	{
		$def = new static();
		
		foreach(get_object_vars($def) as $key => $value) {
			if($key=='properties') {
				continue;
			}
			$def->$key = $attributes[$key]??null;
		}
		
		$property_definitions = Attributes::getClassPropertyDefinition( $reflection, EShopEntity_Definition::class );
		foreach( $property_definitions as $property_name => $definition ) {
			$def->properties[$property_name] = EShopEntity_Definition_Property::read( $definition );
		}
		
		/**
		 * @var EShopEntity_Basic $class
		 */
		$class = $reflection->getName();
		
		$def->entity_type = $class::getEntityType();
		
		return $def;
	}
	
	public static function get( string|object $object_or_class ) : ?static
	{
		$class_name = is_object( $object_or_class ) ? get_class( $object_or_class ) : $object_or_class;
		
		if(!array_key_exists($class_name, static::$definitions)) {
			$reflection = new ReflectionClass($object_or_class);
			
			$attributes = Attributes::getClassDefinition( $reflection, EShopEntity_Definition::class );
			
			static::$definitions[$class_name] = static::read( $reflection, $attributes );
		}
		
		return static::$definitions[$class_name];
	}
	
	public function getAdminManagerInterface(): ?string
	{
		return $this->admin_manager_interface;
	}
	
	public function getEntityNameReadable( bool $translate=false ): ?string
	{
		if($translate) {
			return Tr::_( $this->entity_name_readable, dictionary: Admin_Managers::get( $this->getAdminManagerInterface() )->getModuleManifest()->getName() );
		} else {
			return $this->entity_name_readable;
		}
	}
	
	public function getEntityType(): ?string
	{
		return $this->entity_type;
	}
	
	
	public function getURLTemplate(): ?string
	{
		return $this->URL_template;
	}
	
	
	public function getImages(): ?array
	{
		return $this->images;
	}
	
	public function getDescriptionMode(): ?bool
	{
		return $this->description_mode;
	}
	
	public function getSeparateTabFormShopData(): ?bool
	{
		return $this->separate_tab_form_shop_data;
	}

	
	
	/**
	 * @return EShopEntity_Definition_Property[]
	 */
	public function getProperties(): array
	{
		return $this->properties;
	}
	
	
	
	
}