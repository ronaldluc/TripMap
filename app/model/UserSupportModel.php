<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette;

class UserSupportModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function addFeedback($values, $user_id)
    {
        $this->database->table('feedback')->insert([
            'user_id' => $user_id,
            'category' => $values->category,
            'subject' => $values->subject,
            'message' => $values->message,
        ]);
    }
}