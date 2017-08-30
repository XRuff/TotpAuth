<?php

namespace XRuff\TotpAuth;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use XRuff\App\Model\BaseDbModel;

class QrRepository extends BaseDbModel {

	/**
	 * @param int $userId
	 * @return string|null
	 */
	public function getUserCode($userId)
	{
		$result = $this->findSecret($userId)
		    ->fetch();

		if ($result) {
			return $result->secret;
		} else {
			return null;
		}
	}

	/**
	 * @param int $userId
	 * @return int
	 */
	public function resetSecret($userId): int
	{
		return $this->findSecret($userId)->delete();
	}

	/**
	 * @param int $userId
	 * @param string $secret
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function saveSecret($userId, $secret): ActiveRow
	{
		return $this->save([
			'user_id' => $userId,
			'secret' => $secret,
		]);
	}

	/**
	 * @param int $userId
	 * @return Nette\Database\Table\Selection
	 */
	private function findSecret($userId): Selection
	{
		return $this->findAll()
		    ->where(['user_id' => $userId])
		    ->order('id DESC');
	}
}
