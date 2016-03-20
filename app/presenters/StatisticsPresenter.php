<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    App\Models\StatisticsModel;

class StatisticsPresenter extends BasePresenter
{
    /** @var StatisticsModel @inject*/
    public $statisticsModel;

    protected function startUp()
    {
        parent::startup();
        if (!$this->user->isLoggedIn())
        {
            $this->flashMessage('Pro přístup k mapám je nutné se přihlásit.', 'danger');
            $this->redirect('Login:');
        }
    }

    public function renderDefault()
    {
        $this->template->userStats = $this->statisticsModel->getUserStats($this->user->id);

        $this->template->globalStats = $this->statisticsModel->getglobalStats();
    }

}