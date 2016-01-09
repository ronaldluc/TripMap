<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use App\Model\UserModel;
use Nette;


class UserPresenter extends Nette\Application\UI\Presenter
{
    /** @var RegistrationTalkModel */
    private $UserModel;


    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    protected function createComponentUserRegistrationForm()
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno')
            ->setRequired();

        $form->addPassword('password', 'Heslo')
            ->setRequired();

        $form->addPassword('passwordVerify', 'Heslo pro kontrolu:')
            ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password']);

        $form->addText('email', 'Email')
            ->setRequired()->addRule($form::EMAIL);

        $form->addSubmit('send', 'Registrovat přednášku');

        $form->onSuccess[] = array($this, 'registerTalkFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }


    public function userRegistrationFormSucceeded($form, $values)
    {
        $temp = $this->userModel->createUser($values);

        if ($temp)
        {
            $this->flashMessage('Registrace proběhla úspěšně', 'success');
        } else {
            $this->flashMessage('ÚČASTNÍK s tímto emailem neexistuje', 'danger');
        }
        $this->redirect('this');

    }

}