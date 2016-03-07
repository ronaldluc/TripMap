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

    public function allTrips($user_id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('user_id = ?', $user_id)
            ->order('date DESC')
            ->fetchAll();

        return $value;
    }

    public function getFilteredTrips($user_id, $start, $end, $string)
    {
        $selection = $this->database->table('trip')->where('user_id = ?', $user_id);

        if ($string != NULL) {
            $selection = $selection->where('LCASE(name) LIKE LCASE(?)', '%' . $string . '%');
        }

        if ($start != NULL) {
            $selection = $selection->where('DATE_ADD(date, INTERVAL +duration DAY) >= ?', $start);
        }

        if ($end != NULL) {
            $selection = $selection->where('date <= ?', $end);
        }

        $value = $selection->order('date DESC')->fetchAll();
        return $value;
    }

    public function getFilteredId($user_id, $start, $end, $string)
    {
        $selection = $this->database->table('trip')->where('user_id = ?', $user_id);

        if ($string != NULL) {
            $selection = $selection->where('LCASE(name) LIKE LCASE(?)', '%' . $string . '%');
        }

        if ($start != NULL) {
            $selection = $selection->where('DATE_ADD(date, INTERVAL +duration DAY) >= ?', $start);
        }

        if ($end != NULL) {
            $selection = $selection->where('date <= ?', $end);
        }

        $value = $selection->order('date DESC')->select('id')->fetchAll();
        return $value;
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
            'date' => $trip->date,
            'duration' => $trip->duration,
            'lenght' => $trip->lenght,
        ]);
    }
}