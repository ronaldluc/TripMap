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

            dump($check);
            dump('ahoj!');
//
            $user = $this->database->table('user')->where('username', $values->username)->fetch();
//
            $this->database->table('user_validation')->insert([
                'user_id' => $user->id,
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

    public function newPassword($user_email)
    {
        $newPassword =  Random::generate(10, "a-zA-Z0-9");

        $user = $this->database->table('user')->where('email', $user_email)->fetch();

        if ($user->checked == 1) {
            $this->database->table('user')->where('email', $user_email)->update([
                'password' => Passwords::hash($newPassword),
            ]);

            return $newPassword;
        } elseif ($user->checked)
            return 1;
        else {
            return 0;
        }

    }
}