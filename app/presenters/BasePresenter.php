<?php

namespace App\Presenters;

use Nette,
    App\Models\BaseModel;


class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var BaseModel */
    private $baseModel;

    public function injectBaseModel(BaseModel $baseModel) {
        $this->baseModel = $baseModel;
    }

    protected function beforeRender ()
    {
        parent::beforeRender();
        $user_id = $this->user->id;
        if ($this->user->isLoggedIn() and $user_id != NULL)
        {
            $css = $this->baseModel->getCss($user_id);
            $this->template->userCss = $css->style;
        } else {
            $this->template->userCss = 1;
        }
    }
}
