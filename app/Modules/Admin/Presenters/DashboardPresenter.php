<?php declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Core\Model\EventModel;
use Nette\Utils\DateTime;

final class DashboardPresenter extends AbstractBasePresenter
{
    /**
     * @var EventModel @inject
     */
    public $eventModel;

    public function actionDefault(): void
    {
        $this->template->approvedEventsCounts = $this->eventModel->getAll()
            ->select('COUNT(*) AS all')
            ->select('COUNT(IF(MONTH(start) = MONTH(?), 1, NULL)) AS thisMonth', $datetime = new DateTime)
            ->select('COUNT(IF(MONTH(start) = MONTH(?), 1, NULL)) AS nextMonth', $datetime->modifyClone('+1 MONTH'))
            ->where('start >= ?', $datetime)
            ->where('state = ?', EventModel::STATE_APPROVED)
            ->fetch();

        $this->template->notApprovedEventsCounts = $this->eventModel->getAll()
            ->select('COUNT(*) AS all')
            ->select('COUNT(IF(MONTH(start) = MONTH(?), 1, NULL)) AS thisMonth', $datetime = new DateTime)
            ->select('COUNT(IF(MONTH(start) = MONTH(?), 1, NULL)) AS nextMonth', $datetime->modifyClone('+1 MONTH'))
            ->where('start >= ?', $datetime)
            ->where('state = ?', EventModel::STATE_NOT_APPROVED)
            ->fetch();
    }
}
