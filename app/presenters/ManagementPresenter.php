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

    public $showModal;

    public $invalidateTrips;

    public $filter;

    protected function startUp()
    {
        parent::startup();
        if (!$this->user->isLoggedIn())
        {
            $this->flashMessage('Pro přístup k mapám je nutné se přihlásit.', 'danger');
            $this->redirect('Login:');
        }
    }

    public function renderDefault()
    {
        $id = $this->params['id'];
        if ($id != NULL and $this->showModal == TRUE)
        {
            $chosen = $this->managementModel->getTrip($id);
            $this['editTripForm']->setDefaults([
                'id' => $id,
                'name' => $chosen->name,
                'text' => $chosen->text,
                'date' => $chosen->date->format('Y-m-d'),
                'duration' => $chosen->duration,
                'lenght' => $chosen->lenght,
            ]);
        }

        if ($this->filter != NULL)
        {
            $this->template->trips = $this->managementModel->getFilteredTrips($this->user->id, $this->filter->from, $this->filter->to, $this->filter->name);
        } else {
            $this->template->trips = $this->managementModel->allTrips($this->user->id);
        }

        $this->template->showModal = $this->showModal;

        $this->redrawControl('tripContainer');
        $this->redrawControl('editTripModal');
    }

    public function handleDelete($id)
    {
        $this->managementModel->deleteTrip($id);
        $this->showModal = FALSE;
        $this->invalidateTrips = TRUE;
    }

    public function handleEdit($id)
    {
        $this->showModal = TRUE;
    }

    public function createComponentEditTripForm($id)
    {
        $form = new Nette\Application\UI\Form;

        $form->addText('name', 'Název')
            ->setRequired();

        $form->addHidden('id');

        $form->addTextArea('text', 'Poznámka')
            ->setRequired();

        $form->addText('date', 'Začátek')
            ->setType('date')
            ->setRequired();

        $form->addText('duration', 'Počet dní')
            ->setType('number')
            ->setAttribute('min', 1)
            ->setRequired();

        $form->addText('lenght', 'Délka trasy')
            ->setType('number');

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = array($this, 'editFormSucceeded');

        Helpers::bootstrapForm($form);

        $form->getElementPrototype()->addClass('ajax');

        return $form;
    }

    public function editFormSucceeded($form, $values)
    {
        $this->managementModel->editTrip($values);
        $this->showModal = FALSE;
        $this->invalidateTrips = TRUE;
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
        $this->filter = $values;
        $this->invalidateTrips = TRUE;
    }

    public function handleResetFilter()
    {
        $this->filter = NULL;
        $this->invalidateTrips = TRUE;
    }

    public function handleShowMap()
    {
        if ($this->filter != NULL)
        {
            $tripsId = $this->managementModel->getFilteredId($this->user->id, $this->filter->from, $this->filter->to, $this->filter->name);
        } else {
            $tripsId = $this->managementModel->getFilteredId($this->user->id, NULL, NULL, NULL);
        }

        dump($tripsId);
        die;
    }
}