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
        $value = $selection->where('user_id = ?', $id)->fetchAll();

        return $value;
    }

    public function deleteTrip($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('id = ?', $id)->delete();

        return $value;
    }

}