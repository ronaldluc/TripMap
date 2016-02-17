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
        'date' => new DateTime(),
        'duration' => 10,
        'area' => 2,
        'lenght' => $values->lenght,
        'polygon' => $values->polygon,
        'distance' => 5,
    ]);
//    '[[1860993.2184021152,6315838.773147544],[1836323.1675230744,6315876.991661686],[1798219.308922914,6309742.920141801],[1860993.2184021152,6315838.773147544]]'
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