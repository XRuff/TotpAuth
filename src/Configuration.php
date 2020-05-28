<?php

namespace XRuff\TotpAuth;

use Nette\SmartObject;

class Configuration
{
	use SmartObject;

	/** @var string $issuer */
	public $issuer;

	/** @var int $timeWindow */
	public $timeWindow;

	/** @var string $codeService */
	public $codeService;

	/** @var string $codeSize */
	public $codeSize;

	/**
	 * @param string $issuer
	 * @param int $timeWindow
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
