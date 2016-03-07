<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette,
    Nette\Security as NS,
    Nette\Utils\DateTime,
    Nette\Security\Passwords;

class SettingsModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function editUser($values, $user_id)
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

            /*$mail = new Message;
            $mail->setFrom('BrNOC bot <bot@brnoc.cz>')
                ->addTo($values->email)
                ->setSubject('Potvrzení příhlášení')
                ->setBody("Byl jsi přihlášen jako účastník BrNOCi 2015. \n \nBrNOC tým");*/
        }

        return $check;
    }
}