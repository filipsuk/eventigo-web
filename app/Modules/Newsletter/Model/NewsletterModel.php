<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\BaseModel;


class NewsletterModel extends BaseModel
{
	const TABLE_NAME = 'newsletters'; // TODO migrace

	/**
	 * Get latest newsletter texts
	 *
	 * @return array
	 * @throws \RuntimeException
	 */
	public function getLatest() : array 
	{
		$newsletter = $this->getAll()->order('created DESC')->limit(1);
		if ($newsletter->count() !== 0) {
			return $newsletter->fetch()->toArray();
		} else {
			throw new \RuntimeException('No newsletters found');
		}
	}
}
