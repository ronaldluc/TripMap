<?php
/**
 * @author Ronald Luc
 */

namespace App\Model;

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
}