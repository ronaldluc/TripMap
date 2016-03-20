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
//        $temp = $this->database->table('user')->where('email = ?', $values->email)->fetch();
        $row = $this->database->table('user')->where('id', $user_id)->fetch();

        if (!NS\Passwords::verify($values->oldPassword, $row->password)) {
//            throw new NS\AuthenticationException('Špatné heslo.');
            $check = 0;
        } else {
            if ($values->newPassword == NULL) $values->newPassword = 'a';

            $this->database->table('user')->where('id', $user_id)->update([
                'username' => $values->username,
                'password' => Passwords::hash($values->newPassword),
            ]);
            $check = 1;
        }

//        $check = 0;
//        if ((!$temp)) $check = 1;



//        if ($check) {
//            $this->database->table('user')->where('id', $user_id)->update([
//                'username' => $values->username,
//                'password' => Passwords::hash($values->newPassword),
//            ]);
//
//            /*$mail = new Message;
//            $mail->setFrom('BrNOC bot <bot@brnoc.cz>')
//                ->addTo($values->email)
//                ->setSubject('Potvrzení příhlášení')
//                ->setBody("Byl jsi přihlášen jako účastník BrNOCi 2015. \n \nBrNOC tým");*/
//        }

        return $check;
    }

    public function changeCss($type, $user_id)
    {
        $this->database->table('user')->where('id', $user_id)->update([
            'style' => $type,
        ]);
    }
}