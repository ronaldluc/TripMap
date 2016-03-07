<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Models\RegistrationModel,
    Helpers;


class SettingsPresenter extends Nette\Application\UI\Presenter
{
    /** @var RegistrationModel */
    private $settingsModel; //TODO change to best practise

    public function __construct(RegistrationModel $settingsModel)
    {
        $this->settingsModel = $settingsModel;
    }

    protected function createComponentEditUserForm()
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno')
            ->setRequired();

        $form->addPassword('oldPassword', 'Původní heslo')
            ->setRequired();

        $form->addPassword('newPassword', 'Nové heslo')
            ->setRequired();

        $form->addPassword('newPasswordVerify', 'Nové heslo pro kontrolu:')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['newPassword']);


        $form->addSubmit('send', 'Změnit údaje');

        $form->onSuccess[] = array($this, 'editUserFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }


    public function editUserFormSucceeded($form, $values)
    {
        $temp = $this->settingsModel->editUser($values, $this->user->id);

        if ($temp)
        {
            $this->flashMessage('Změna údajů proběhla úspěšně', 'success');
        } else {
            $this->flashMessage('Změna se nezdařila, špatné heslo', 'danger');
        }
        $this->redirect('this');

    }

}