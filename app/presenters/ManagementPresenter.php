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
        $this->template->trips = $this->managementModel->allTrips($this->user->id);
    }

    public function handleDelete($id)
    {
        $this->managementModel->deleteTrip($id);
    }

    public function handleChange($values)
    {
        $form = new Nette\Application\UI\Form;

        $form->getElementPrototype()->class = "ajax";

        $form->addText('name', 'Název')
            ->setRequired()
            ->setDefaultValue($values->name);

        $form->addText('text', 'Poznámka')
            ->setRequired();

        $form->addText('lenght', 'Délka trasy');

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = function ($form, $values) { $this->redrawControl("newTrip"); };
    }

}