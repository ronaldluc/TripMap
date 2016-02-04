<?php
/**
 * @author Ronald Luc
 */

namespace App\Presenters;

use App\Model\AuthenticatorModel;
use Nette,
    Nette\Application\UI\Form,
    Helpers,
    App\Model\MapModel;


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


    protected function createComponentMapForm()
    {
        $form = new Nette\Application\UI\Form;

        $form->addHidden('polygon');

        $form->addText('test');

        $form->onSuccess[] = array($this, 'mapFormSucceeded');

        Helpers::bootstrapForm($form);

        return $form;
    }

    public function mapFormSucceeded($form)
    {
        dump($form->values->test);
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