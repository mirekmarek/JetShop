<?php
namespace JetShop;

use Jet\BaseObject;
use Jet\Data_Text;
use Jet\IO_File;
use Jet\SysConf_Path;

class Exports_FileGenerator_XML extends BaseObject
{
	protected string $input_charset = 'UTF-8';

	protected string $output_charset = 'UTF-8';

	protected string $target_path = '';

	protected string $tmp_file_path = '';

	public function __construct( string $export_code, string $shop_code, string $target_path )
	{
		$this->target_path = $target_path;

		$this->tmp_file_path = SysConf_Path::getTmp().$export_code.'_'.$shop_code.'_'.date('YmdHis').'.xml';
	}

	public function getInputCharset(): string
	{
		return $this->input_charset;
	}

	public function setInputCharset( string $input_charset ): void
	{
		$this->input_charset = $input_charset;
	}

	public function getOutputCharset(): string
	{
		return $this->output_charset;
	}

	public function setOutputCharset( string $output_charset ): void
	{
		$this->output_charset = $output_charset;
	}

	public function getTargetPath(): string
	{
		return $this->target_path;
	}

	public function setTargetPath( string $target_path ): void
	{
		$this->target_path = $target_path;
	}

	public function getTmpFilePath(): string
	{
		return $this->tmp_file_path;
	}

	public function setTmpFilePath( string $tmp_file_path ): void
	{
		$this->tmp_file_path = $tmp_file_path;
	}


	public function createStart() : void
	{
		IO_File::write($this->tmp_file_path, '');
	}

	public function createDone() : void
	{
		IO_File::copy( $this->tmp_file_path, $this->target_path );
		IO_File::delete( $this->tmp_file_path );
	}


	protected function tagsParam( string $param_name, string $param_value, int $ident=1 ) : void
	{
		$this->tagStart('PARAM', $ident);
		$this->tagPair('PARAM_NAME', $param_name, $ident+1);
		$this->tagPair('VAL', $param_value, $ident+1);
		$this->tagEnd('PARAM', $ident);

	}

	protected function tagStart( string $tag, int $ident = 0, array $attributes = [] ) : void
	{
		if($ident>0) {
			$ident = str_pad('', $ident, "\t");
		} else {
			$ident = '';
		}

		$_attrs = [];

		foreach( $attributes as $k=> $v ) {
			$_attrs[] = ' '.$k.'="'.Data_Text::htmlSpecialChars($v).'"';
		}

		$attributes = implode('', $_attrs);

		$this->_line( $ident."<{$tag}{$attributes}>" );
	}

	protected function tagEnd( string $tag, $ident = 0 ) : void
	{
		if($ident>0) {
			$ident = str_pad('', $ident, "\t");
		} else {
			$ident = '';
		}

		$this->_line( $ident."</{$tag}>" );
	}


	protected function tagPair( string $tag, string $value, int $ident=1, bool $encode=true, array $attributes=[]  ) : void
	{
		if($ident>0) {
			$tabs = str_pad('', $ident, "\t");
		} else {
			$tabs = '';
		}

		if($encode) {
			$value = Data_Text::htmlSpecialChars($value);
		}

		$_attrs = [];

		foreach( $attributes as $k=> $v ) {
			$_attrs[] = ' '.$k.'="'.Data_Text::htmlSpecialChars($v).'"';
		}

		$attributes = implode('', $_attrs);


		$this->_line( $tabs."<{$tag}{$attributes}>{$value}</$tag>" );
	}

	protected function _line( string $line ) : void
	{
		if($this->input_charset!=$this->output_charset) {
			$line = iconv($this->input_charset, $this->output_charset, $line);
		}

		$line .= PHP_EOL;

		IO_File::append( $this->tmp_file_path, $line );
	}



}