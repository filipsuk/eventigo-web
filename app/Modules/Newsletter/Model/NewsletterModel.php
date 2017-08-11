<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Modules\Core\Model\AbstractBaseModel;
use App\Modules\Newsletter\Model\Entity\Newsletter;
use Nette\Database\Table\IRow;
use RuntimeException;

final class NewsletterModel extends AbstractBaseModel
{
    /**
     * @var string
     */
    protected const TABLE_NAME = 'newsletters'; // TODO migrace

    /**
     * Get latest newsletter texts.
     *
     * @throws RuntimeException
     *
     * @return mixed[]
     */
    public function getLatest(): array
    {
        $newsletter = $this->getAll()
            ->order('created DESC')
            ->limit(1);

        if ($newsletter->count() !== 0) {
            return $newsletter->fetch()->toArray();
        }

        throw new RuntimeException('No newsletters found');
    }

    public function createNewsletter(Newsletter $newsletter): IRow
    {
        return $this->insert([
            'subject' => $newsletter->getSubject(),
            'from' => $newsletter->getFrom(),
            'intro_text' => $newsletter->getIntroText(),
            'outro_text' => $newsletter->getOutroText(),
            'author' => $newsletter->getAuthor(),
        ]);
    }
}
