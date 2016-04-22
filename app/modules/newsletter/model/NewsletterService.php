<?php

namespace App\Modules\Newsletter\Model;


use Nette\Utils\DateTime;
use SendGrid;
use SendGrid\Email;

class NewsletterService
{
	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

	/** @var string */
	private $apiKey;


	public function setApiKey(string $apiKey) : self
	{
		$this->apiKey = $apiKey;
		return $this;
	}


	/**
	 * @param int $userId
	 * @param string $from
	 * @param string $subject
	 * @param string $content
	 * @return bool|int|\Nette\Database\Table\IRow
	 */
	public function createNewsletter($userId, $from, $subject, $content)
	{
		return $this->userNewsletterModel->createNewsletter($userId, $from, $subject, $content);
	}

	/**
	 * @param int[] $usersNewslettersIds
	 */
	public function sendNewsletters(array $usersNewslettersIds)
	{
		$usersNewsletters = $this->userNewsletterModel->getAll()->wherePrimary($usersNewslettersIds)->fetchAll();
		foreach ($usersNewsletters as $userNewsletter) {
			$sendGrid = new SendGrid($this->apiKey);
			$email = new Email;

			$email->addTo($userNewsletter->user->email)
				->setFrom($userNewsletter->from)
				->setSubject($userNewsletter->subject)
				->setHtml($userNewsletter->content);

			try {
				$sendGrid->send($email);
				$this->userNewsletterModel->getAll()->wherePrimary($userNewsletter->id)->update([
					'sent' => new DateTime,
				]);

			} catch (SendGrid\Exception $e) {
				// TODO log unsuccessful email send
			}
		}
	}
}