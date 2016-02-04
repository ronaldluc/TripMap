<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Model\RegistrationModel,
    Helpers;


class RegistrationPresenter extends Nette\Application\UI\Presenter
{
    /** @var RegistrationModel */
    private $registrationModel; //TODO change to best practise


    public function __construct(RegistrationModel $registrationModel)
    {
        $this->registrationModel = $registrationModel;
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

        $form->addSubmit('send', 'Registrovat se');

        $form->onSuccess[] = array($this, 'registrationFormSucceeded');

    Helpers::bootstrapForm($form);

        return $form;
    }


    public function registrationFormSucceeded($form, $values)
    {
        $temp = $this->registrationModel->createUser($values);

        if ($temp)
        {
            $this->flashMessage('Registrace proběhla úspěšně', 'success');
        } else {
            $this->flashMessage('ÚČASTNÍK s tímto emailem neexistuje', 'danger');
        }
        $this->redirect('this');

    }

}