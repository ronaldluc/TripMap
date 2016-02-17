<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette;

class StatisticsModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getMaxArea($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('user_id = ?', $id)->max('area');

        return $value;
    }

    public function getMaxLenght($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('user_id = ?', $id)->max('lenght');

        return $value;
    }


}