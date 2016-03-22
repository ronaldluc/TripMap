<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Models\RegistrationModel,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer,
    Helpers;


class RegistrationPresenter extends LoginPresenter
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
        $check = $this->registrationModel->createUser($values);

        if ($check)
        {
            $url = $this->link('activation', $check, $values->username);

            $mail = new Message;

            $mail->setFrom('bot@tripmap.cz', 'TripMap bot')
                ->addTo($values->email, $values->username)
                ->setSubject('Registrace na TripMap.cz')
                ->setHTMLBody(
                    "Vážený uživateli<br><br>
                    pro dokončení registrace na webu TripMap.cz je nutné aktivovat účet. Na tento email Ti budou chodit zprávy o veškerých změnách Tvého účtu a v případě zapomenutí hesla
                    Ti na tento email ti přijde nové.<br><br>

                    <a href=\"www.tripmap.cz$url\">Odkaz pro aktivaci: www.tripmap.cz$url.</a> <br><br>

                    Příjemné používání aplikace
                    <a href=\"mailto:ronald.luc@tripmap.cz\"></a>Ronald Luc</a>"
                );

            $mailer = new SendmailMailer;

            $mailer->send($mail);

            $this->flashMessage('Registrace proběhla úspěšně, byl ti odeslán aktivační email', 'success');
        } else {
            $this->flashMessage('Toto uživatelské jméno je už zabrané', 'danger');
        }
        $this->redirect('this');

    }

    public function renderActivation($check, $user_name)
    {
        if ($this->registrationModel->validateUser($check, $user_name)) {
            $this->flashMessage('Účet úspěšně aktivován', 'success');
        } else {
            $this->flashMessage('Klíče se neshodují '.$check, 'danger');
        }
        $this->redirect('Login:');
    }

}