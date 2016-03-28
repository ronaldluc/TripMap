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

            $mail->setFrom('system@tripmap.cz', 'TripMap')
                ->addTo($values->email, $values->username)
                ->setSubject('Registrace | TripMap.cz')
                ->setHTMLBody(
                    "Vážený uživateli,<br><br>
                    pro dokončení registrace na webu TripMap.cz je nutné účet aktivovat. Na tento email Ti budou chodit zprávy o veškerých změnách Tvého účtu a v případě zapomenutí hesla
                    Ti na tento email přijde nové.<br><br>

                    Odkaz pro aktivaci: <a href=\"http://www.tripmap.cz$url\">http://www.tripmap.cz$url.</a> <br><br>

                    Aplikace je stále v aktivním vývoji, můžeš počítat s novinkami každý týden.<br><br>

                    Příjemné používání aplikace<br>
                    <a href=\"mailto:ron.norik@gmail.com\">Ronald Luc</a> "
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
        if ($check and $user_name) {
            if ($this->registrationModel->validateUser($check, $user_name)) {
                $this->flashMessage('Účet úspěšně aktivován', 'success');
            } else {
                $this->flashMessage('Klíče se neshodují', 'danger');
            }
        }
        $this->redirect('Login:guide');
    }

    protected function createComponentNewPasswordForm()
    {
        $form = new Form;

        $form->addText('email', 'Email')
            ->setRequired()->addRule($form::EMAIL);

        $form->addSubmit('send', 'Zaslat nové heslo');

        $form->onSuccess[] = array($this, 'newPasswordFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }

    public function newPasswordFormSucceeded($form, $values)
    {
        $newPassword = $this->registrationModel->newPassword($values->email);

        if ($newPassword) {
            $mail = new Message;

            $mail->setFrom('system@tripmap.cz', 'TripMap')
                ->addTo($values->email)
                ->setSubject('Obnovení hesla | TripMap.cz')
                ->setHTMLBody(
                    "Vážený uživateli,<br><br>
                    zažádal jsi o obnovení hesla na <a href=\"http://www.tripmap.cz\">webu TripMap.cz</a>. Tvoje nové heslo je:<br><br>

                    $newPassword<br><br>

                    V nastavení si ho můžeš změnit.<br><br>

                    Příjemné používání aplikace<br>
                    <a href=\"mailto:ron.norik@gmail.com\">Ronald Luc</a> "
                );

            $mailer = new SendmailMailer;

            $mailer->send($mail);

            $this->flashMessage('By ti zaslán email s novým heslem', 'success');
        } else {
            $this->flashMessage('Neexistující email', 'danger');
        }

        $this->redirect('Login:default');
    }

}