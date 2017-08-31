<?php

namespace XRuff\TotpAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Nette\Database\Table\ActiveRow;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;
use Oops\TotpAuthenticator\Security\TotpAuthenticator;
use Tracy\Debugger;

class Auth {

	/** @var Configuration $config */
	private $config;

	/** @var QrRepository $qrRepository */
	private $qrRepository;

	/** @var Oops\TotpAuthenticator\Security\TotpAuthenticator $totpAuthenticator */
	private $totpAuthenticator;

	/** @var String $secret */
	private $secret;

	/** @var User $user */
	private $user;

	/** @var Session $session */
	private $session;

	public function __construct(Configuration $config, QrRepository $qrRepository, User $user)
	{
		$this->config = $config;
		$this->qrRepository = $qrRepository;
		$this->user = $user;
		$this->totpAuthenticator = (new TotpAuthenticator)
			->setIssuer($config->issuer)
			->setTimeWindow($config->timeWindow);
	}

	/**
	 * @return string
	 */
	public function getSecret(): string
	{
		return $this->secret;
	}

	/**
	 * @return Auth
	 */
	public function setSession(Session $session): Auth
	{
		$this->session = $session;
		return $this;
	}

	/**
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function saveSecret(): ActiveRow
	{
		$secret = $this->getSessionSection()->secret;
		dump($this->qrRepository
			->saveSecret($this->user->id, $secret ? $secret : $this->secret));
		return $this->qrRepository
			->saveSecret($this->user->id, $secret ? $secret : $this->secret);
	}

	/**
	 * @return int
	 */
	public function resetSecret(): int
	{
		$this->getSessionSection()->secret = null;
		return $this->qrRepository
			->resetSecret($this->user->id);
	}

	/**
	 * @return bool|string
	 */
	public function hasSecret()
	{
		return $this->qrRepository
			->getUserCode($this->user->id);
	}

	/**
	 * @return string|null
	 */
	public function getQrBase64(): string
	{
		return $this->hasSecret() ? null : 'data:image/png;base64,' . base64_encode($this->getQr());
	}

	/**
	 * @return bool
	 */
	public function verify($code): bool
	{
		$secret = $this->qrRepository
			->getUserCode($this->user->id);

		if ($secret) {
			return $this->totpAuthenticator->verifyCode($code, $secret);
		} else {
			return false;
		}
	}

	/**
	 * @return Nette\Http\SessionSection
	 */
	private function getSessionSection(): SessionSection
	{
		return $this->session->getSection('totpAuth');
	}

	/**
	 * @return string
	 */
	private function getRandomSecret(): string
	{
		$this->secret = $this->totpAuthenticator->getRandomSecret();
		$this->getSessionSection()->secret = $this->secret;

		return $this->secret;
	}

	/**
	 * @return string
	 */
	private function getTotpUri(): string
	{
		if (!$this->user->isLoggedIn()) {
			throw new Exception('User is not logged. Is not possible to generate TOTP URI.');
		}
		$secret = $this->getRandomSecret();
		return $this->totpAuthenticator->getTotpUri($secret, $this->user->identity->username);
	}

	/**
	 * @return string
	 */
	private function getQrUri(): string
	{
		return $this->config->codeService . 'chs=' . $this->config->codeSize . '&cht=qr&chl=' . $this->getTotpUri() . '%2F&choe=UTF-8';
	}

	/**
	 * @return GuzzleHttp\Psr7\Stream
	 */
	private function getQr(): Stream
	{
		$client = new Client();

		try {
			$res = $client->request('GET', $this->getQrUri());
		} catch (ClientException $e) {
			Debugger::log($e, 'totpAuth');
			throw new Exception($e);
		}

		return $res->getBody();
	}
}
