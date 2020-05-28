<?php

declare(strict_types = 1);

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

	/** @var string $identityKey */
	public $identityKey;

	/**
	 * @param string $issuer
	 * @param int $timeWindow
	 * @param string $codeService
	 * @param string $codeSize
	 * @param string $identityKey
	 */
	public function __construct(
		$issuer,
		$timeWindow,
		$codeService,
		$codeSize,
		$identityKey
	)
	{
		$this->issuer = $issuer;
		$this->timeWindow = $timeWindow;
		$this->codeService = $codeService;
		$this->codeSize = $codeSize;
		$this->identityKey = $identityKey;
	}
}
