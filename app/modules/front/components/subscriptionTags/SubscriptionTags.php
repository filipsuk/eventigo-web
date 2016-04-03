<?php

namespace App\Modules\Front\Components\SubscriptionTags;

use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Utils\Collection;
use App\Modules\Front\Components\Subscription\Subscription;
use App\Modules\Front\Model\Exceptions\Subscription\EmailExistsException;
use Kdyby\Translation\Translator;
use Nette\Database\Helpers;
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
		$this->tags = $this->tagModel->getAllByMostEvents();
	}


	public function render()
	{
		$this->template->tags = $this->tags->fetchPairs('code');
		parent::render();
	}


	public function createComponentForm()
	{
		$form = parent::createComponentForm();

		$tagsGroups = $this->tags->fetchAssoc('tagGroupName|id');
		$tagsContainer = $form->addContainer('tags');
		foreach ($tagsGroups as $tagGroupName => $tagsGroup) {
			$tagsContainer->addCheckboxList($tagGroupName)
				->setItems(Helpers::toPairs($tagsGroups[$tagGroupName], 'code', 'name'))
				->setTranslator(NULL);
		}
		$form->addHidden('real_subscribe'); // For stupid Firefox not submitting "subscribe" input in POST

		return $form;
	}


	/**
	 * @throws EmailExistsException
	 */
	public function processForm(Form $form)
	{
		$values = $form->getValues();
		if (array_key_exists('real_subscribe', $values) && $values['real_subscribe'] === 'true') {
			$user = $this->subscribe($values->email);
			if (!$user) {
				return;
			}

			// Store user's selected tags
			$chosenTags = Collection::getNestedValues($values->tags);
			$tags = $this->tagModel->getAll()->where('code IN (?)', $chosenTags)->fetchAll();
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
