<?php
namespace Radical\CLI\Cron\Jobs\Interfaces;

interface ICronJob {
	function execute(array $arguments);
	function getName();
}