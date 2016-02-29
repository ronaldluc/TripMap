<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use Nette,
    App\Models\ManagementModel,
    Nette\Application\UI\Form,
    Helpers;

class ManagementPresenter extends Nette\Application\UI\Presenter
{
    /** @var ManagementModel @inject*/
    public $managementModel;

    public function renderDefault()
    {
        $this->template->showModal = FALSE;
        if ($this->isAjax()) {
            $id = $this->params['id'];
            $chosen = $this->managementModel->getTrip($id);

            $this['editTripForm']->setDefaults([
                'id' => $id,
                'name' => $chosen->name,
                'text' => $chosen->text,
                'lenght' => $id,
            ]);
            $this->template->showModal = TRUE;
            $this->redrawControl('editTripModal');
        } else {
            $this->template->trips = $this->managementModel->allTrips($this->user->id);
        }

//        $this->template->trips = $this->isAjax()
//            ? array()
//            : $this->managementModel->allTrips($this->user->id);
    }

    public function handleDelete($id)
    {
        $this->managementModel->deleteTrip($id);

        $this->redrawControl('tripContainer');
    }

    public function handleEdit($id)
    {

 //přidat vyskakovací modal

//        $this['editTripForm']->setDefaults([
//            'name'=> 'Tom',
//            'text' => 'Kroll',
//            'lenght' => $id,
//        ]);
//        $this->redrawControl('editTripModal');

//        $this->createComponentEditTripForm($id);  <= zkoušel jsem to předat Formu takhle

    }

    public function createComponentEditTripForm($id)
    {
        $form = new Nette\Application\UI\Form;

        $form->addText('name', 'Název')
            ->setRequired()
//            ->setDefaultValue($this->template->trips[$id]->name)
        ;
        $form->addHidden('id');

        $form->addText('text', 'Poznámka')
            ->setRequired();

        $form->addText('lenght', 'Délka trasy');

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = array($this, 'editFormSucceeded');
//        $form->onSuccess[] = function ($form, $values) { $this->redrawControl("editModal"); };

        Helpers::bootstrapForm($form);

        $form->getElementPrototype()->addClass('ajax');

        return $form;
    }

    public function editFormSucceeded($form, $values)
    {
        $this->managementModel->editTrip($values);

        $this->redrawControl('editTripModal');
        $this->redrawControl('tripContainer');
    }

    public function createComponentFilterForm()
    {
        $form = new Nette\Application\UI\Form;

        $form->addText('name', 'Název')
            ->setType('search');

        $form->addText('from', 'Od')
            ->setType('date');

        $form->addText('to', 'Do')
            ->setType('date');

        $form->addSubmit('send', 'Vyhledat');

        $form->onSuccess[] = array($this, 'filterFormSucceeded');


        Helpers::bootstrapForm($form);

        $form->getElementPrototype()->addClass('ajax');

        return $form;
    }


    public function filterFormSucceeded($form, $values)
    {
//        $temp = $this->registrationModel->createUser($values);
//
//        if ($temp)
//        {
//            $this->flashMessage('Registrace proběhla úspěšně', 'success');
//        } else {
//            $this->flashMessage('ÚČASTNÍK s tímto emailem neexistuje', 'danger');
//        }
//        $this->redirect('this');

    }
}