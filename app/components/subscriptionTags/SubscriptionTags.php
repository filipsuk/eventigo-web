<?php

namespace App\Components\SubscriptionTags;

use App\Components\Subscription\Subscription;
use App\Model\Exceptions\Subscription\EmailExistsException;
use App\Model\TagModel;
use App\Model\UserTagModel;
use App\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\Database\Table\IRow;


class SubscriptionTags extends Subscription
{
	/** @var TagModel */
	private $tagModel;

	/** @var UserTagModel */
	private $userTagModel;


	public function __construct(Translator $translator, UserModel $userModel, TagModel $tagModel, UserTagModel $userTagModel)
	{
		parent::__construct($translator, $userModel);
		$this->tagModel = $tagModel;
		$this->userTagModel = $userTagModel;
	}


	public function createComponentForm() : Form
	{
		$form = parent::createComponentForm();

		$tags = $this->tagModel->getAll()->fetchAssoc('code=name');
		$form->addCheckboxList('tags')->setItems($tags)->setTranslator(NULL);

		return $form;
	}


	public function processForm(Form $form) : IRow
	{
		$values = $form->getValues();

		try {
			// Store user's email
			$user = parent::processForm($form);

			// Store user's selected tags
			$tags = $this->tagModel->getAll()->where('code IN (?)', $values->tags)->fetchAll();
			foreach ($tags as $tag) {
				$this->userTagModel->insert([
					'tag_id' => $tag->id,
					'user_id' => $user->id,
				]);
			}

			$this->onSuccess($values->email);

		} catch (EmailExistsException $e) {
			$this->onExists($values->email);
		}
	}
}