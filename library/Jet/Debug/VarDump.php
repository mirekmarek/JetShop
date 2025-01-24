<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace Jet;

use Closure;

require_once 'ErrorHandler/Error/BacktraceItem.php';
require_once 'VarDump/BacktraceItem.php';


class Debug_VarDump
{
	
	protected array $vars = [];
	
	/**
	 * @var Debug_VarDump_BacktraceItem[]
	 */
	protected array $backtrace = [];
	
	public static array $var_dumps = [];
	
	public static bool $displayer_registered = false;
	
	protected static ?Closure $displayer = null;
	
	public function __construct( array $vars )
	{
		$this->vars = $vars;
		$this->backtrace = Debug_Profiler_Run::getBacktrace( 2 );
		
		static::$var_dumps[] = $this;
		
		if( !static::$displayer_registered ) {
			register_shutdown_function(function() {
				if( static::$displayer ) {
					$displayer = static::$displayer;
					
					$displayer( static::$var_dumps );
				}
			});
			static::$displayer_registered = true;
		}
	}
	
	public function getCation() : string
	{
		return $this->backtrace[0]->getFileDisplayable().':'.$this->backtrace[0]->getLine();
	}
	
	public function getVars(): array
	{
		return $this->vars;
	}
	
	/**
	 * @return Debug_VarDump_BacktraceItem[]
	 */
	public function getBacktrace(): array
	{
		return $this->backtrace;
	}
	
	public static function getDisplayer(): ?Closure
	{
		return self::$displayer;
	}
	
	public static function setDisplayer( ?Closure $displayer ): void
	{
		self::$displayer = $displayer;
	}
	
	public static function getVarDumps(): array
	{
		return self::$var_dumps;
	}
	
	
	
}