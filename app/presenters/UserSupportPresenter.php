<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Models\UserSupportModel,
    Helpers;

class UserSupportPresenter extends BasePresenter
{
    /** @var UserSupportModel */
    private $userSupportModel; //TODO change to best practise

    public function __construct(UserSupportModel $userSupportModel)
    {
        $this->userSupportModel = $userSupportModel;
    }

    public function createComponentSupportForm()
    {
        $form = new Form;

        $form->addRadioList('category', 'Typ', [
            '0' => 'Aplikace padá',
            '1' => 'Aplikace nefunguje správně',
            '2' => 'Návrh na změnu',
        ])->setRequired();

        $form->addText('subject', 'Předmět')
            ->setRequired();

        $form->addTextArea('message', 'Popis')
            ->setRequired();

        $form->addSubmit('send', 'Nahlásit chybu');

        $form->onSuccess[] = array($this, 'supportFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }

    public function supportFormSucceeded($form, $values)
    {
        $this->userSupportModel->addFeedback($values, $this->user->id);

        $this->flashMessage('Zpětná vazba byla odeslána úspěšně', 'success');

        $this->redirect('this');
    }
}