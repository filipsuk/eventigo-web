<?php declare(strict_types=1);

namespace App\Modules\Front\Components\Tags;

use App\Modules\Core\Components\AbstractBaseControl;
use App\Modules\Core\Components\Form\Form;
use App\Modules\Core\Model\TagGroupModel;
use App\Modules\Core\Model\TagModel;
use App\Modules\Core\Model\UserTagModel;
use App\Modules\Core\Utils\Collection;
use Nette\Database\Helpers;
use Nette\Database\Table\Selection;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\User;

final class Tags extends AbstractBaseControl
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

    /**
     * @var User
     */
    private $user;

    public function __construct(
        TagModel $tagModel,
        UserTagModel $userTagModel,
        TagGroupModel $tagGroupModel,
        User $user
    ) {
        parent::__construct();
        $this->tagModel = $tagModel;
        $this->userTagModel = $userTagModel;
        $this->tagGroupModel = $tagGroupModel;
        $this->tags = $this->tagModel->getAllByMostEvents();
        $this->user = $user;
    }

    public function render(): void
    {
        $this->template->tags = $this->tags->fetchPairs('code');
        $this->template->tagsGroups = $this->tagGroupModel->getAll()
            ->where('icon IS NOT NULL')
            ->fetchPairs('name', 'icon');
        $this->template->columnsPerRow = 4;
        parent::render();
    }

    public function processForm(Form $form): void
    {
        $values = $form->getValues();

        // Store user's selected tags
        $chosenTags = Collection::getNestedValues((array) $values->tags);
        $tags = $this->tagModel->getAll()->where('code IN (?)', $chosenTags)->fetchAll();

        foreach ($tags as $tag) {
            try {
                // Add newly checked tags
                $this->userTagModel->insert([
                    'tag_id' => $tag->id,
                    'user_id' => $this->user->getId(),
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // Tag has been already inserted
            }
        }

        // Remove not checked tags
        if ($chosenTags) {
            $this->userTagModel->delete([
                'user_id' => $this->user->getId(),
                'tag_id NOT IN (?)' => $this->tagModel->getAll()
                    ->where('code IN (?)', $chosenTags)
                    ->fetchPairs(null, 'id'),
            ]);
        } else {
            $this->userTagModel->delete([
                'user_id' => $this->user->getId(),
            ]);
        }

        $this->onChange();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;

        $tagsGroups = $this->tags->fetchAssoc('tagGroupName|id');
        $tagsContainer = $form->addContainer('tags');
        foreach ($tagsGroups as $tagGroupName => $tagsGroup) {
            $tagsContainer->addCheckboxList($tagGroupName)
                ->setItems(Helpers::toPairs($tagsGroups[$tagGroupName], 'code', 'name'))
                ->setTranslator(null);
        }

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }
}
