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
    if(!$values->category) $values->category = NULL;
    $this->database->table($this->tableName)->insert([
        'user_id' => $userId,
        'name' => $values->name,
        'text' => $values->text,
        'category_id' => $values->category,
        'date' => $values->date,
        'duration' => $values->duration,
        'area' => 2,
        'lenght' => $values->lenght,
        'polygon' => $values->polygon,
        'distance' => 5,
    ]);
}
    public function editTrip($trip, $user_id)
    {
        if(!$trip->category) $trip->category = NULL;
        $selection = $this->database->table('trip');
        $selection->where('user_id', $user_id)->where('id = ?', $trip->id)->fetch()->update([
            'name' => $trip->name,
            'text' => $trip->text,
            'date' => $trip->date,
            'duration' => $trip->duration,
            'lenght' => $trip->lenght,
            'category_id' => $trip->category,
        ]);
    }

    public function changeTrip($values, $id)
    {
        $this->database->table($this->tableName)->where('id = ?', $id)->fetch()->update([
            'polygon' => $values
        ]);
    }

    public function deleteTrip($id)
    {
        $this->database->table($this->tableName)->where('id', $id)->delete();
    }

    public function loadTrips($user_id)
    {
        $selection = $this->database->table('trip');
        $trips = $selection->where('user_id = ?', $user_id)->fetchAll();

        return $trips;
    }

    public function loadCategories($user_id)
    {
        $categories = $this->database->table('category')->where('user_id', $user_id)->fetchAll();

        return $categories;
    }
}