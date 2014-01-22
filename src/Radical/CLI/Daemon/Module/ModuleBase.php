<?php
namespace Radical\CLI\Daemon\Module;

abstract class ModuleBase {
	protected $autoRestart = true;
	
	/**
	 * The name of the daemon.
	 * Used in process titles
	 * 
	 * @return string
	 */
	function getName(){
		return array_pop(explode('\\',get_called_class()));
	}
	
	/**
	 * Daemon main loop
	 * 
	 * @param array $parameters
	 */
	abstract function loop($parameters);
	function execute(array $parameters){
		while(true){
			if($this->autoRestart){
				if(pcntl_fork()){
					do{
						$pid = pcntl_wait($status);
					}while($pid == -1);
				}
			}
			
			$continue = true;
			while($continue){
				$continue = !$this->Loop($parameters);
			}
		}
	}
}