<?php

namespace XRuff\TotpAuth\DI;

use Nette;
use Nette\Utils\Validators;

class TotpAuthExtension extends Nette\DI\CompilerExtension
{
	/** @var array $DEFAULTS */
	private static $DEFAULTS = [
		'issuer' => null,
		'timeWindow' => 1,
		'codeService' => 'https://chart.googleapis.com/chart?',
		'codeSize' => '300x300',
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$DEFAULTS);

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

	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
	    $container = $this->getContainerBuilder();
	    $initialize = $class->getMethod('initialize');
	    $initialize->addBody('$service = $this->getByType("XRuff\TotpAuth\Auth");');
	    $initialize->addBody('$service->setSession($this->getByType("Nette\Http\Session"));');
	}

	/**
	 * @param Nette\Configurator $configurator
	 */
	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('totpAuth', new TotpAuthExtension());
		};
	}
}
