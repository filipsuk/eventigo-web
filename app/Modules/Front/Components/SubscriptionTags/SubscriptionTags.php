<?php declare(strict_types=1);

namespace App\Modules\Front\Components\SubscriptionTags;

use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\TagGroupModel;
use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Utils\Collection;
use App\Modules\Front\Components\Subscription\Subscription;
use App\Modules\Front\Model\Exceptions\Subscription\EmailExistsException;
use Kdyby\Translation\Translator;
use Nette\Database\Helpers;
use Nette\Database\Table\Selection;

final class SubscriptionTags extends Subscription
{
    /**
     * @var callable[]
     */
    public $onChange = [];

    /**
     * @var TagModel
     */
    private $tagModel;

    /**
     * @var UserTagModel
     */
    private $userTagModel;

    /**
     * @var Selection
     */
    private $tags;

    /**
     * @var TagGroupModel
     */
    private $tagGroupModel;

    public function __construct(
        UserModel $userModel,
        TagModel $tagModel,
        UserTagModel $userTagModel,
        TagGroupModel $tagGroupModel
    ) {
        parent::__construct($userModel);
        $this->tagModel = $tagModel;
        $this->userTagModel = $userTagModel;
        $this->tagGroupModel = $tagGroupModel;
        $this->tags = $this->tagModel->getByMostEvents();
    }

    public function render(): void
    {
        $this->template->tags = $this->tags->fetchPairs('code');
        $this->template->tagsGroups = $this->tagGroupModel->getAll()
            ->where('icon IS NOT NULL')
            ->fetchPairs('name', 'icon');
        parent::render();
    }

    /**
     * @throws EmailExistsException
     */
    public function processForm(Form $form): void
    {
        $values = $form->getValues();
        if (array_key_exists('real_subscribe', $values) && $values['real_subscribe'] === 'true') {
            $user = $this->subscribe($values->email);
            if (! $user) {
                return;
            }

            // TODO move to user tag service
            // Store user's selected tags
            $chosenTags = Collection::getNestedValues((array) $values->tags);
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
            $section = $this->presenter->getSession($this->getPresenter()->getAction() === 'discover'
                ? 'discover'
                : 'subscriptionTags');
            $section->tags = $values->tags;
            $this->onChange();
        }
    }

    protected function createComponentForm(): Form
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
}
