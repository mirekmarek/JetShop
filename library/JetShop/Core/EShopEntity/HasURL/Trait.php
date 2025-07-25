<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_Text;
use Jet\Http_Headers;
use Jet\MVC;
use JetApplication\EShopEntity_Definition;

trait Core_EShopEntity_HasURL_Trait {
	
	public function checkURL( string $URL_path ) : bool
	{
		if( $this->generateURLPathPart()!=$URL_path ) {
			MVC::getRouter()->setIsRedirect( $this->getURL(), Http_Headers::CODE_301_MOVED_PERMANENTLY );
			return false;
		}
		
		return true;
	}
	
	public function getURL( array $GET_params=[] ) : string
	{
		return $this->getEshop()->getURL(
			[$this->generateURLPathPart()],
			$GET_params
		);
	}
	
	public function generateURLPathPart() : string
	{
		$name = $this->getURLNameDataSource();
		
		$template = EShopEntity_Definition::get($this)->getURLTemplate();
		
		$name = Data_Text::removeAccents( $name );
		
		$name = strtolower($name);
		$name = preg_replace('/([^0-9a-zA-Z \-\_])+/', '', $name);
		$name = preg_replace( '/([[:blank:]])+/', '-', $name);
		$name = str_replace('_', '-', $name);
		$name = str_replace('--', '-', $name);
		$name = str_replace('--', '-', $name);
		
		$id = method_exists( $this, 'getEntityId' ) ? $this->getEntityId() : $this->getID();
		
		return Data_Text::replaceData( $template, [
			'NAME' => $name,
			'ID' => $id
		] );
		
	}
	
	public static function getIdByURLPathPart( ?string $URL_path ) : ?int
	{
		$template = EShopEntity_Definition::get(static::class)->getURLTemplate();
		$reg_exp =  Data_Text::replaceData( $template, [
			'NAME' => '',
			'ID' => '([0-9]+)'
		] );
		$reg_exp = str_replace('.', '\.', $reg_exp);
		$reg_exp = '/'.$reg_exp.'$/';
		
		
		if(!preg_match($reg_exp, $URL_path, $res)) {
			return null;
		}
		
		$id = (int)$res[1];

		return $id;
	}
	
	
	public function generateURLParam( string $name, string $url_param_property='url_param' ) : string
	{
		$name = Data_Text::removeAccents( $name );
		
		$name = strtolower($name);
		$name = preg_replace('/([^0-9a-zA-Z ])+/', '', $name);
		$name = preg_replace( '/([[:blank:]])+/', '-', $name);
		
		
		$min_len = 2;
		
		$parts = explode('-', $name);
		$valid_parts = array();
		foreach( $parts as $value ) {
			
			if (strlen($value) > $min_len) {
				$valid_parts[] = $value;
			}
		}
		
		$url_param_base = count($valid_parts) > 1 ? implode('-', $valid_parts) : $name;
		$url_param = $url_param_base;
		
		$exists = function() use (&$url_param, $url_param_base, $url_param_property) : bool
		{
			$where = $this->getEshop()->getWhere();
			$where[] = 'AND';
			$where[$url_param_property]=$url_param;
			$where[] = 'AND';
			$where['entity_id !='] = $this->entity_id;
			
			return (bool)count(static::dataFetchCol(['entity_id'], $where));
		};
		
		$suffix = 0;
		while($exists()) {
			$suffix++;
			$url_param = $url_param_base.$suffix;
		}
		
		return $url_param;
		
		
	}
	
	
}