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
    Nette\Utils\DateTime,
    Nette\Utils;


class MapPresenter extends BasePresenter
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

    public function renderDefault()
    {
        $trips =  $this->mapModel->loadTrips($this->user->id);

        $categories = $this->mapModel->loadCategories($this->user->id);

        $newTrips = [];

        foreach ($trips as $trip) {

            if ($trip->duration == 1) {
                $duration = $trip->duration.' den';
            } elseif ($trip->duration < 5) {
                $duration = $trip->duration.' dny';
            } else {
                $duration = $trip->duration.' dní';
            }

            if ($trip->category_id) {
                $color = [
                    'red' => $categories[$trip->category_id]->red,
                    'green' => $categories[$trip->category_id]->green,
                    'blue' => $categories[$trip->category_id]->blue,
                ];
            } else {
                $color = [
                    'red' => 0,
                    'green' => 0,
                    'blue' => 0,
                ];
            }

            $lol = Utils\Strings::truncate($trip->name, 20);

            $newTrips[] = [
                'id' => $trip->id,
                'polygon' => $trip->polygon,
                'color' => $color,
                'info' => ['name' => Utils\Strings::truncate($trip->name, 25),
                    'date' => $trip->date->format('d. m. Y'),
                    'length' => $trip->lenght?$trip->lenght.' km':'',
                    'duration' => $duration,
                    'category' => $trip->category_id?$categories[$trip->category_id]->name:NULL
                ],
            ];
        }

        $this->template->trips = $newTrips;

        $this->template->showModal = FALSE;
        $this['newTripForm']->setDefaults([
            'name' => ' ',
            'text' => ' ',
            'lenght' => ' ',
        ]);

        $this->redrawControl("newTrip");
    }

    protected function createComponentNewTripForm()
    {
        $categories = $this->mapModel->loadCategories($this->user->id);

        $categoryNames[NULL] = 'Žádná';
        foreach ($categories as $category) {
            $categoryNames[$category->id] = $category->name;
        }

        $form = new Nette\Application\UI\Form;

        $form->addGroup();

        $form->addHidden('polygon');

        $form->addText('name', 'Název')
            ->setRequired();

        $form->addTextArea('text', 'Poznámka');

        $form->addText('date', 'Začátek')
            ->setType('date')
            ->setAttribute('value', new DateTime())
            ->setRequired();

        $form->addText('duration', 'Počet dní')
            ->setType('number')
            ->setAttribute('min', 1)
            ->setAttribute('value', 1)
            ->setRequired();

        $form->addText('lenght', 'Délka trasy');

        $form->addSelect('category', 'Kategorie', $categoryNames);

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

    public function handleChangeTrip()
    {
        $polygon = $this->getHttpRequest()->getPost('trip');
        $id = $this->getHttpRequest()->getPost('id');
        $modified = Helpers::modifyPolygon($polygon);
        $this->mapModel->changeTrip($modified, $id);
    }

    public function actionTest()
    {
        $kvak = [
            'lol' => 40,
            'boj' => 'asdlkj'
        ];
        $this->sendJson($kvak);
    }

}