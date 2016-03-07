<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use App\Models\AuthenticatorModel;
use Nette,
    Nette\Application\UI\Form,
    Helpers,
    App\Models\MapModel,
    Nette\Utils\DateTime;


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

        $form->addHidden('polygon');

        $form->addText('name', 'Název')
            ->setRequired();

        $form->addTextArea('text', 'Poznámka')
            ->setRequired();

        $form->addText('date', 'Začátek')
            ->setType('date')
            ->setAttribute('value', new DateTime());

        $form->addText('duration', 'Počet dní')
            ->setType('number')
            ->setAttribute('min', 1)
            ->setAttribute('value', 1)
            ->setRequired();

        $form->addText('lenght', 'Délka trasy');

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = array($this, 'newTripFormSucceeded');

        Helpers::bootstrapForm($form);

        $form->getElementPrototype()->addClass('ajax');

        return $form;
    }

    public function newTripFormSucceeded($form, $values)
    {
        $this->mapModel->addNewTrip($values, $this->user->id);

//        $this->redirect('this');
    }

    public function handleNewTrip($trip)
    {
        $this->mapModel->addTrip($trip, $this->user->id);
    }
//        $this->redirect('default');
//        dump($polygon);


    public function renderDefault()
    {
        $this->template->trips = $this->mapModel->loadTrips($this->user->id);

        $this->template->showModal = FALSE;
        $this['newTripForm']->setDefaults([
            'name' => ' ',
            'text' => ' ',
            'lenght' => ' ',
        ]);

        $this->redrawControl("newTrip");

    }

    public function handleChangeTrip()
    {
        $polygon = $this->getHttpRequest()->getPost('trip');
        $id = $this->getHttpRequest()->getPost('id');
        $modified = Helpers::modifyPolygon($polygon);
        $this->mapModel->changeTrip($modified, $id);
    }

}