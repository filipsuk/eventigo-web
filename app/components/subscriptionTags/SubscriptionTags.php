<?php

namespace App\Components\SubscriptionTags;

use App\Components\Subscription\Subscription;
use App\Model\Exceptions\Subscription\EmailExistsException;
use App\Model\TagModel;
use App\Model\UserTagModel;
use App\Model\UserModel;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\Database\Table\Selection;


class SubscriptionTags extends Subscription
{
	/** @var TagModel */
	private $tagModel;

	/** @var UserTagModel */
	private $userTagModel;

	/** @var array */
	public $onChange = [];

	/** @var Selection */
	private $tags;


	public function __construct(Translator $translator, UserModel $userModel, TagModel $tagModel, UserTagModel $userTagModel)
	{
		parent::__construct($translator, $userModel);
		$this->tagModel = $tagModel;
		$this->userTagModel = $userTagModel;
	}


	public function render()
	{
		$this->template->tags = $this->tags->fetchPairs('code');
		parent::render();
	}


	public function createComponentForm()
	{
		$form = parent::createComponentForm();

		$this->tags = $this->tagModel->getAllByMostEvents();
		$form->addCheckboxList('tags')
			->setItems($this->tags->fetchPairs('code', 'name'))
			->setTranslator(NULL);

		return $form;
	}


	/**
	 * @throws EmailExistsException
	 */
	public function processForm(Form $form)
	{
		$values = $form->getValues();
		$httpData = $form->getHttpData();

		if (array_key_exists('subscribe', $httpData)) {
			$user = $this->subscribe($values->email);
			if (!$user) {
				return;
			}

			// Store user's selected tags
			$tags = $this->tagModel->getAll()->where('code IN (?)', $values->tags)->fetchAll();
			foreach ($tags as $tag) {
				$this->userTagModel->insert([
					'tag_id' => $tag->id,
					'user_id' => $user->id,
				]);
			}

			$this->onSuccess($user->email);

		} else {
			// Store tags to session
			$section = $this->presenter->getSession('subscriptionTags');
			$section->tags = $values->tags;
			$this->onChange();
		}
	}
}