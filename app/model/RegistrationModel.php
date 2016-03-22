<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette,
    Nette\Security as NS,
    Nette\Utils\DateTime,
    Nette\Security\Passwords,
    Nette\Utils\Random;

class RegistrationModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function createUser($values)
    {
        $temp = $this->database->table('user')->where('email = ?', $values->email)->fetch();
        $temp2 = $this->database->table('user')->where('username = ?', $values->username)->fetch();

        $check = 0;
        if ((!$temp)and(!$temp2)) $check = 1;

        if ($check) {
            $this->database->table('user')->insert([
                'username' => $values->username,
                'password' => Passwords::hash($values->password),
                'email' => $values->email,
                'joined' => new DateTime(),

            ]);

            $check =  Random::generate(10, "a-zA-Z0-9");

            $user_id = $this->database->table('user')->where('username', $values->username)->fetch();

            $this->database->table('user_validation')->insert([
                'user_id' => $user_id,
                'key' => $check,
            ]);
        }

        return $check;
    }

    public function validateUser($check, $user_name)
    {
        $user = $this->database->table('user')->where('username', $user_name)->fetch();
        $key = $this->database->table('user_validation')->where('user_id', $user->id)->fetch();

        if ($key->key == $check) {
            $this->database->table('user')->where('id', $user->id)->update([
                'checked' => 1,
            ]);
            $this->database->table('user_validation')->where('user_id', $user->id)->delete();

            return 1;
        }
        return 0;
    }
}