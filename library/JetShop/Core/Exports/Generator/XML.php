<?php
namespace JetShop;

use Jet\Data_Text;

use JetApplication\Exports_Generator;

abstract class Core_Exports_Generator_XML extends Exports_Generator
{

	protected int $ident = 0;

	protected function _ident() : string
	{
		if($this->ident>0) {
			$ident = str_pad('', $this->ident, "\t");
		} else {
			$ident = '';
		}

		return $ident;
	}

	protected function _attributes( array $attributes ) : string
	{
		if(!$attributes) {
			return '';
		}

		$_attrs = [];

		foreach( $attributes as $k=> $v ) {
			$_attrs[] = ' '.$k.'="'.Data_Text::htmlSpecialChars($v).'"';
		}

		return implode('', $_attrs);
	}

	public function start(): void
	{
		parent::start();
		if(!$this->tmp_file_path) {
			header('Content-Type: text/xml;charset='.$this->output_charset);
		}
		$this->_line('<?xml version="1.0" encoding="'.$this->output_charset.'"?>');


	}

	public function resetIdent( int $ident=0 ) : void
	{
		$this->ident = $ident;
	}

	public function tagStart( string $tag, array $attributes = [] ) : void
	{
		$attributes = $this->_attributes($attributes);

		$this->_line( $this->_ident()."<{$tag}{$attributes}>" );

		$this->ident++;
	}

	public function tagEnd( string $tag ) : void
	{
		$this->ident--;
		$this->_line( $this->_ident()."</{$tag}>" );
	}


	public function tagPair( string $tag, string $value, bool $encode=true, array $attributes=[]  ) : void
	{
		if($encode) {
			$value = Data_Text::htmlSpecialChars($value);
		}

		$attributes = $this->_attributes($attributes);

		$this->_line( $this->_ident()."<{$tag}{$attributes}>{$value}</$tag>" );
	}

	public function tagsParam( string $param_name, string $param_value ) : void
	{
		$this->tagStart('PARAM');
		$this->tagPair('PARAM_NAME', $param_name);
		$this->tagPair('VAL', $param_value);
		$this->tagEnd('PARAM');
	}

}