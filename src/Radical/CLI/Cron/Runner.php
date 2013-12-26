<?php
namespace Radical\CLI\Cron;

use Radical\Basic\ClassInterface;
class Runner {
	private $job;
	private $ns;
	
	function __construct($job, $ns = null){
		$this->job = $job;
		$this->ns = $ns;
	}
	
	private function getClass(){
		$class = '\\'.$this->ns.'\\CLI\\Cron\\Jobs\\'.$this->job;
		return $class;
	}
	
	function isValid(){
		if(class_exists($this->getClass())){
			if(ClassInterface::oneof($this->getClass(), '\\Radical\\CLI\\Cron\\Jobs\\Interfaces\\ICronJob')){
				return true;
			}
		}
		return false;
	}
	
	function run(array $arguments = array()){
		$class = $this->getClass();
		$instance = new $class();
		if($instance instanceof Jobs\Interfaces\ICronJob){
			$instance->Execute($arguments);
		}
	}
}