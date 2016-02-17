<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use App\Models\AuthenticatorModel;
use Nette,
    Nette\Application\UI\Form,
    Helpers,
    App\Models\MapModel;


class MapPresenter extends Nette\Application\UI\Presenter
{
    /** @var MapModel @inject*/
    public $mapModel;

    /** MapPresenter constructor*/
    protected function startUp()
    {
        parent::startup();
        if (!$this->user->isLoggedIn())
        {
            $this->flashMessage('Pro přístup k mapám je nutné se přihlásit.', 'danger');
            $this->redirect('Login:');
        }
    }


    protected function createComponentNewTripForm()
    {
        $form = new Nette\Application\UI\Form;

        $form->getElementPrototype()->class = "ajax";

        $form->addHidden('polygon');

        $form->addText('name', 'Název')
            ->setRequired();

        $form->addText('text', 'Poznámka')
            ->setRequired();

        $form->addText('lenght', 'Délka trasy');

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = array($this, 'newTripFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }

    public function newTripFormSucceeded($form, $values)
    {
        $this->redrawControl("newTrip");
        $this->mapModel->addNewTrip($values, $this->user->id);
    }

    public function handleNewTrip($trip) {
        $this->mapModel->addTrip($trip, $this->user->id);
    }
//        $this->redirect('default');
//        dump($polygon);


    public function renderDefault() {
        $this->template->trips = $this->mapModel->loadTrips($this->user->id);
    }

}