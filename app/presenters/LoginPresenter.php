<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use App\Models\AuthenticatorModel;
use Nette,
    Nette\Application\UI\Form,
    Helpers;


class LoginPresenter extends Nette\Application\UI\Presenter
{
    /** @var AuthenticatorModel */
    private $authenticatorModel;


    public function __construct(AuthenticatorModel $authenticatorModel)
    {
        $this->authenticatorModel = $authenticatorModel;
    }

    protected function createComponentLoginForm()
    {
        $form = new Nette\Application\UI\Form;

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired();

        $form->addPassword('password', 'Heslo:')
            ->setRequired();

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = array($this, 'loginFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }

    public function loginFormSucceeded($form)
    {
        //$this->user->setAuthenticator(AuthenticatorModel); zkoušel jsem, nic nefaká
        try {
            //$this->user->AuthenticatorModel;
            $this->getUser()->login($form->values->username, $form->values->password);
            $this->flashMessage('Jsi přihlášen.', 'success');
            $this->redirect('Homepage:');
        } catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
        }
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.', 'success');
        $this->redirect('Homepage:');
    }

}