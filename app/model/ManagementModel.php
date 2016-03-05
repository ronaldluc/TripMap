<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette;

class ManagementModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function allTrips($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('user_id = ?', $id)
            ->order('date DESC')
            ->fetchAll();

        return $value;
    }

    public function getFilteredTrips($id, $start, $end, $string)
    {

    }

    public function getTrip($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('id = ?', $id)->fetch();

        return $value;
    }

    public function deleteTrip($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('id = ?', $id)->delete();

        return $value;
    }

    public function editTrip($trip)
    {
        $selection = $this->database->table('trip');
        $selection->where('id = ?', $trip->id)->fetch()->update([
            'name' => $trip->name,
            'text' => $trip->text,
            'lenght' => $trip->lenght,
        ]);
    }
}