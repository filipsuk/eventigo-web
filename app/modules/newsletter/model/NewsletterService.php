<?php

namespace App\Modules\Newsletter\Model;

use Latte\Loaders\StringLoader;
use Nette\Application\UI\ITemplateFactory;


class NewsletterService
{
	/** @var NewsletterModel @inject */
	public $newsletterModel;

	/** @var UserNewsletterModel @inject */
	public $userNewsletterModel;

	/** @var ITemplateFactory @inject */
	public $templateFactory;


	/**
	 * @param int $newsletterId
	 * @param int $userId
	 * @return \Nette\Database\Table\IRow
	 */
	public function createNewsletter(int $newsletterId, int $userId)
	{
		$newsletter = $this->newsletterModel->getAll()->wherePrimary($newsletterId)->fetch();
		$html = $this->render($newsletter->layout->layout, $newsletter->content->content);

		return $this->userNewsletterModel->insert([
			'user_id' => $userId,
			'newsletter_id' => $newsletterId,
			'hash' => $this->userNewsletterModel->generateUniqueHash(),
			'html' => $html,
		]);
	}


	/**
	 * Render latte templates to HTML as a string
	 * @param string $layout
	 * @param string $content
	 * @return string
	 */
	public function render($layout, $content)
	{
		$contentTemplate = $this->templateFactory->createTemplate();
		$contentString = $contentTemplate->getLatte()
			->setLoader(new StringLoader())
			->renderToString($content, ['_control' => $this]);

		$template = $this->templateFactory->createTemplate();
		return $template->getLatte()
			->setLoader(new StringLoader())
			->renderToString($layout, ['content' => $contentString]);
	}
}