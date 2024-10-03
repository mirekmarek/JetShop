<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use Jet\AJAX;
use Jet\Factory_MVC;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\MVC_View;
use JetApplication\AdministratorSignatures;

abstract class Handler_Note_MessageGenerator {
	protected MVC_View $view;
	protected ReturnOfGoods $return_of_goods;
	protected static ?array $generators = null;
	
	public function __construct( MVC_View $view, ReturnOfGoods $return_of_goods )
	{
		$view_dir = $view->getScriptsDir().'message/'.$return_of_goods->getShopKey().'/'.$this->getKey().'/';
		if(!IO_Dir::exists($view_dir)) {
			IO_Dir::create( $view_dir );
		}
		
		$this->view = Factory_MVC::getViewInstance( $view_dir );
		
		$this->return_of_goods = $return_of_goods;
		
		$this->init();
	}
	
	protected function init() : void
	{
	
	}
	
	public function getKey() : string
	{
		$class_name = static::class;
		
		$base = Handler_Note_MessageGenerator::class.'_';
		
		return substr( $class_name, strlen($base) );
	}
	
	
	abstract public function getTitle() : string;
	
	abstract public function generateSubject() : string;
	
	public function renderSubject() : string
	{
		return trim($this->view->render('subject'));
	}
	
	abstract public function generateText() : string;
	
	public function renderText( bool $append_signature) : string
	{
		$text = $this->view->render('text');
		
		if($append_signature) {
			$text .= "\n\n".AdministratorSignatures::getSignature( $this->return_of_goods->getShop() );
		}
		
		return $text;
	}
	
	
	public static function initGenerators( MVC_View $view, ReturnOfGoods $return_of_goods ) : void
	{
		$files = IO_Dir::getList( __DIR__.'/MessageGenerator', '*.php' );
		
		static::$generators = [];
		foreach($files as $name) {
			$class_name = Handler_Note_MessageGenerator::class.'_'.substr($name,0,-4);
			
			/**
			 * @var Handler_Note_MessageGenerator $generator
			 */
			$generator = new $class_name( $view, $return_of_goods );
			static::$generators[$generator->getKey()] = $generator;
		}
	}
	
	/**
	 * @return static[]
	 */
	public static function getGenerators() : array
	{
		return static::$generators;
	}
	
	public static function handleMessageGenerators() : void
	{
		$key = Http_Request::GET()->getString('generate_message');
		$generators = static::getGenerators();
		if(
			$key &&
			isset($generators[$key])
		) {
			$generator = $generators[$key];
			
			AJAX::commonResponse([
				'subject' => $generator->generateSubject(),
				'text' => $generator->generateText(),
			]);
		}
	}
	
}