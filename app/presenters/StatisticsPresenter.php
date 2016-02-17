<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    App\Models\StatisticsModel;

class StatisticsPresenter extends Nette\Application\UI\Presenter
{
    /** @var StatisticsModel @inject*/
    public $statisticsModel;

    public function renderDefault()
    {
        $this->template->maxArea = $this->statisticsModel->getMaxArea($this->user->id);
    }

}