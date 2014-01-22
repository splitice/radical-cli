<?php
namespace Radical\CLI\Daemon;

use Radical\Basic\ClassInterface;
class Runner {
	private $module;
	private $ns;
	
	function __construct($module, $ns = null){
		$this->module = $module;
		$this->ns = $ns;
	}
	
	private function getClass(){
		$ns = $this->ns;
		if($this->ns === null){
			$ns = '*';
		}
		$class = '\\'.$ns.'\\CLI\\Daemon\\Module\\'.$this->module;

		if($this->ns === null){
			$classes = \Radical\Core\Libraries::get($class);
			if($classes){
				return $classes[0];
			}
			return null;
		}
		
		return $class;
	}
	
	function isValid(){
		if(class_exists($this->getClass())){
			if(ClassInterface::oneof($this->getClass(), '\\Radical\\CLI\\Daemon\\Module\\Interfaces\\IModuleJob')){
				return true;
			}
		}
		return false;
	}
	
	
	function run(array $arguments){
		$class = $this->getClass();
		$instance = new $class();
		if($instance instanceof Module\Interfaces\IModuleJob){
			$instance->Execute($arguments);
		}
	}
}