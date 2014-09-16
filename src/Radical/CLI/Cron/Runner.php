<?php
namespace Radical\CLI\Cron;

use Radical\Basic\ClassInterface;
use Splitice\EventTrait\THookable;

class Runner {
    use THookable;
	private $job;
	private $ns;

    function __construct($job, $ns = null){
        $this->hookInit();
		$this->job = $job;
		$this->ns = $ns;
	}
	
	private function getClass(){
		$ns = $this->ns;
		if($this->ns === null){
			$ns = '*';
		}
		$class = '\\'.$ns.'\\CLI\\Cron\\Jobs\\'.$this->job;
		
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
            $this->call_action('before_run', array('class'=>$class, 'arguments'=>$arguments));
			$instance->Execute($arguments);
            $this->call_action('after_run', array('class'=>$class, 'arguments'=>$arguments));
		}
	}
}