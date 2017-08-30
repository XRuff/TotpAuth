<?php

namespace XRuff\TotpAuth;

use Nette\Object;

class Configuration extends Object
{
	/** @var string $issuer */
	public $issuer;

	/** @var string $timeWindow */
	public $timeWindow;

	/** @var string $codeService */
	public $codeService;

	/** @var string $codeSize */
	public $codeSize;

	/**
	 * @param string $issuer
	 * @param string $timeWindow
	 * @param string $codeService
	 * @param string $codeSize
	 */
	public function __construct(
		$issuer,
		$timeWindow,
		$codeService,
		$codeSize
	)
	{
		$this->issuer = $issuer;
		$this->timeWindow = $timeWindow;
		$this->codeService = $codeService;
		$this->codeSize = $codeSize;
	}
}
