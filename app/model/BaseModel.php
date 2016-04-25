<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette;


class BaseModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getCss($user_id)
    {
        $selection = $this->database->table('user');
        $value = $selection->where('id', $user_id)
            ->fetch();

        return $value;
    }
}