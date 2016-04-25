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

    public function getFilteredTrips($user_id, $start, $end, $string, $category_id)
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

        if ($category_id != NULL) {
            $selection = $selection->where('category_id', $category_id);
        }

        $value = $selection->order('date DESC')->fetchAll();
        return $value;
    }

    public function getFilteredId($user_id, $start, $end, $string, $category_id)
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

        if ($category_id != NULL) {
            $selection = $selection->where('category_id', $category_id);
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
        if(!$trip->category) $trip->category = NULL;
        $selection = $this->database->table('trip');
        $selection->where('id = ?', $trip->id)->fetch()->update([
            'name' => $trip->name,
            'text' => $trip->text,
            'date' => $trip->date,
            'duration' => $trip->duration,
            'lenght' => $trip->lenght,
            'category_id' => $trip->category,
        ]);
    }

    public function newCategory($values, $user_id)
    {
        $this->database->table('category')->insert([
            'user_id' => $user_id,
            'name' => $values['name'],
            'red' => $values['red'],
            'green' => $values['green'],
            'blue' => $values['blue'],
        ]);
    }

    public function editCategory($values, $user_id)
    {
        $this->database->table('category')->where('id', $values['id'])->update([
            'name' => $values['name'],
            'red' => $values['red'],
            'green' => $values['green'],
            'blue' => $values['blue'],
        ]);
    }

    public function loadCategories($user_id)
    {
        $categories = $this->database->table('category')->where('user_id', $user_id)->fetchAll();

        return $categories;
    }

    public function getCategory($category_id)
    {
        $category = $this->database->table('category')->where('id', $category_id)->fetch();

        return $category;
    }

    public function deleteCategory($category_id)
    {
        $this->database->table('category')->where('id', $category_id)->delete();
    }
}