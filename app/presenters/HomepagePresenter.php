<?php

namespace App\Presenters;

use Nette,
    App\Models\RegistrationModel,
    App\Models\AuthenticatorModel,
    Helpers,
    Nette\Application\UI\Form,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;


class HomepagePresenter extends RegistrationPresenter
{

    public function renderDefault()
    {
        if ($this->user->isLoggedIn())
        {
            $this->redirect('Homepage:news');
        }
    }
}
