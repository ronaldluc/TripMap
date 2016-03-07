<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette,
    Nette\Utils\DateTime;

class MapModel
{
    private $database;

    private $tableName = 'trip';

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function addTrip($trip, $userId)
    {
        $this->database->table($this->tableName)->insert([
            'user_id' => $userId,
            'name' => 'plainName',
            'date' => new DateTime(),
            'duration' => 50,
            'area' => 4,
            'lenght' => 10,
            'polygon' => $trip,
            'distance' => 16,
        ]);
    }

    public function addNewTrip($values, $userId)
{
    $this->database->table($this->tableName)->insert([
        'user_id' => $userId,
        'name' => $values->name,
        'text' => $values->text,
        'date' => $values->date,
        'duration' => $values->duration,
        'area' => 2,
        'lenght' => $values->lenght,
        'polygon' => $values->polygon,
        'distance' => 5,
    ]);
}

    public function changeTrip($values, $id)
    {
        $this->database->table($this->tableName)->where('id = ?', $id)->fetch()->update([
            'polygon' => $values
        ]);
    }

    public function loadTrips($id)
    {
        $selection = $this->database->table('trip');
        $trips = $selection->where('user_id = ?', $id)->fetchAll();

        return $trips;
    }
}