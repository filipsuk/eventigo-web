<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\BaseModel;
use App\Modules\Newsletter\Model\Entity\Newsletter;
use Nette\Database\Table\IRow;


class NewsletterModel extends BaseModel
{
	const TABLE_NAME = 'newsletters'; // TODO migrace

	/**
	 * Get latest newsletter texts
	 *
	 * @throws \RuntimeException
	 */
	public function getLatest(): array
	{
		$newsletter = $this->getAll()->order('created DESC')->limit(1);
		if ($newsletter->count() !== 0) {
			return $newsletter->fetch()->toArray();
		} else {
			throw new \RuntimeException('No newsletters found');
		}
	}

	public function createNewsletter(Newsletter $newsletter): IRow
	{
		return $this->insert([
			'subject' => $newsletter->getSubject(),
			'from' => $newsletter->getFrom(),
			'intro_text' => $newsletter->getIntroText(),
			'outro_text' => $newsletter->getOutroText(),
			'author' => $newsletter->getAuthor()
		]);
	}
}
