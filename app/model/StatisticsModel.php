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

    public function getUserMaxArea($user_id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('user_id = ?', $user_id)->max('area');

        return $value;
    }

    public function getMaxLenght($id)
    {
        $selection = $this->database->table('trip');
        $value = $selection->where('user_id = ?', $id)->max('lenght');

        return $value;
    }

    public function getUserStats($user_id)
    {
        $selection = $this->database->table('trip')->where('user_id', $user_id);
        $value = [
            'maxArea' => $selection->max('area'),
            'maxLenght' => $selection->max('lenght'),
            'maxDuration' => $selection->max('duration'),
            'last' => $selection->max('date'),
            'first' => $selection->min('date'),
            'avgArea' => $selection->aggregation('AVG(area)'),
            'avgLenght' => $selection->aggregation('AVG(lenght)'),
            'avgDuration' =>$selection->aggregation('AVG(duration)'),
        ];

        return $value;
    }

    public function getGlobalStats()
    {
        $selection = $this->database->table('trip');
        $value = [
            'maxArea' => $selection->max('area'),
            'maxLenght' => $selection->max('lenght'),
            'maxDuration' => $selection->max('duration'),
            'last' => $selection->max('date'),
            'first' => $selection->min('date'),
            'avgArea' => $selection->aggregation('AVG(area)'),
            'avgLenght' => $selection->aggregation('AVG(lenght)'),
            'avgDuration' =>$selection->aggregation('AVG(duration)'),
        ];

        return $value;
    }


}