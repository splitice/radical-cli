<?php
namespace Radical\CLI\Cron\Jobs;

use Radical\Cache\PooledCache;

abstract class CrontabJob implements Interfaces\ICronJob {
	abstract function getInterval();
	abstract protected function _Execute(array $arguments);
	
	private function getTime(){
		$time = 0;
		switch($this->getInterval()){
			case 'minutely':
			case 'hourly':
			case 'daily':
			case 'weekly':
				$time = 60;
			case 'hourly':
			case 'daily':
			case 'weekly':
				$time *= 60;
			case 'daily':
			case 'weekly':
				$time *= 24;
			case 'weekly':
				$time *= 7;
				break;
			default:
				throw new \Exception('Unknown Interval: '.$this->getInterval());
		}
		return $time;
	}
	
	function execute(array $arguments){
		if(!isset($_SERVER['REQUEST_URI'])){
			$this->_Execute($arguments);
		}else{
			$key = '__cron__'.$this->getName();//because pool isnt working for file
			$fileCache = PooledCache::Get('cron', 'FileCache');
			$lastExecute = (int)$fileCache->Get($key);
			$lastWantTo = time() - $this->getTime();
			if(!$lastExecute || $lastExecute < $lastWantTo){
				$this->_Execute($arguments);
				$fileCache->Set($key,time());
				return true;
			}else{
				return false;
			}
		}
	}
}