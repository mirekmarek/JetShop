<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShop_Managers;

abstract class Core_EShop_CookieSettings_Group
{
	public const STATS = 'stats';
	public const MARKETING = 'marketing';
	public const MESUREMENT = 'mesurement';
	
	protected string $code = '';
	
	protected string $title = '';

	protected string $description = '';
	
	protected bool $checked = false;
	
	protected ?bool $enabled = null;
	
	
	public function getCode() : string
	{
		return $this->code;
	}
	
	public function setCode( string $code ) : void
	{
		$this->code = $code;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setTitle( string $title ) : void
	{
		$this->title = $title;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}
	
	public function getChecked() : bool
	{
		return $this->checked;
	}
	
	public function setChecked( bool $checked ) : void
	{
		$this->checked = $checked;
	}
	
	
	public function getEnabled() : bool
	{
		return EShop_Managers::CookieSettings()->groupEnabled($this->code);
	}
	
	public function enable() : void
	{
		EShop_Managers::CookieSettings()->enableGroup( $this->code );
	}
	
	public function disable() : void
	{
		EShop_Managers::CookieSettings()->disableGroup($this->code);
	}
	
}