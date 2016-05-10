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

    public function createComponentSurveyForm()
    {
        $form = new Nette\Application\UI\Form;

        $form->addRadioList('mark', 'Aplikaci', [
            1 => 'navštěvuji denně',
            2 => 'používám každý týden',
            3 => 'jen když jsem někde byl',
            4 => 'vidím poprvé a znova ji nepoužiji',
        ])->setAttribute('class', 'zelená');

        $form->addCheckboxList('mostly', 'Nejčastěji', [
            1 => 'chodím na tůry do lesa',
            2 => 'trávím čas u vody',
            3 => 'cestuji do zahraničí',
            4 => 'sedím doma',
        ])->setAttribute('class', 'zelená');

        $form->addCheckboxList('future', 'Chci', [
            1 => 'sdílení výletů',
            2 => 'filtrovat výlety v mapě',
            3 => 'modul na geoCaching',
        ])->setAttribute('class', 'zelená');

        $form->addSubmit('send', 'Odeslat');

        Helpers::bootstrapForm($form);

        return $form;
    }
}