<?php

namespace XRuff\TotpAuth\DI;

use Nette;
use Nette\Utils\Validators;

class TotpAuthExtension extends Nette\DI\CompilerExtension
{
	/** @var array<string, int|string|null> $defaults */
	private $defaults = [
		'issuer' => null,
		'timeWindow' => 1,
		'codeService' => 'https://chart.googleapis.com/chart?',
		'codeSize' => '300x300',
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$this->validateConfig($this->defaults);

		$config = $this->config;

		Validators::assert($config['issuer'], 'string', 'issuer');
		Validators::assert($config['codeService'], 'string', 'codeService');
		Validators::assert($config['codeSize'], 'string', 'codeSize');

		$configuration = $builder->addDefinition($this->prefix('config'))
			->setClass('XRuff\TotpAuth\Configuration')
			->setArguments([
				$config['issuer'],
				$config['timeWindow'],
				$config['codeService'],
				$config['codeSize'],
			]);

		$builder->addDefinition($this->prefix('qrRepository'))
			->setClass('XRuff\TotpAuth\QrRepository');

		$builder->addDefinition($this->prefix('auth'))
			->setClass('XRuff\TotpAuth\Auth');
	}

	public function afterCompile(Nette\PhpGenerator\ClassType $class): void
	{
	    $container = $this->getContainerBuilder();
	    $initialize = $class->getMethod('initialize');
	    $initialize->addBody('$service = $this->getByType("XRuff\TotpAuth\Auth");');
	    $initialize->addBody('$service->setSession($this->getByType("Nette\Http\Session"));');
	}
}
