<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Models\SettingsModel,
    App\Models\AuthenticatorModel,
    Helpers;


class SettingsPresenter extends BasePresenter
{
    /** @var SettingsModel */
    private $settingsModel; //TODO change to best practise

    /** @var AuthenticatorModel */
    private $authenticatorModel;

    public function __construct(SettingsModel $settingsModel, AuthenticatorModel $authenticatorModel)
    {
        $this->settingsModel = $settingsModel;
        $this->authenticatorModel = $authenticatorModel;
    }

    protected function startUp()
    {
        parent::startup();
        if (!$this->user->isLoggedIn())
        {
            $this->flashMessage('Pro přístup k mapám je nutné se přihlásit.', 'danger');
            $this->redirect('Login:');
        }
    }

    protected function createComponentEditUserForm()
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno');

        $form->addPassword('oldPassword', 'Původní heslo')
            ->setRequired();

        $form->addPassword('newPassword', 'Nové heslo');

        $form->addPassword('newPasswordVerify', 'Nové heslo pro kontrolu:')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['newPassword']);


        $form->addSubmit('send', 'Změnit údaje');

        $form->onSuccess[] = array($this, 'editUserFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }

    public function editUserFormSucceeded($form, $values)
    {
        $check = $this->settingsModel->editUser($values, $this->user->id);
        if ($check) {
            $this->flashMessage('Změna údajů proběhla úspěšně', 'success');
        } else {
            $this->flashMessage('Změna se nezdařila, špatné heslo', 'danger');
        }

        $this->redirect('this');
    }

    public function handleChangeCss($type)
    {
        dump($type);
        $this->settingsModel->changeCss($type, $this->user->id);



//        $this->redirect($this);
    }


}