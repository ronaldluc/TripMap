<?php
/**
 * @author Ronald Luc
 */

namespace App\Model;

use Nette,
    Nette\Security as NS,
    Nette\Utils\DateTime,
    Nette\Security\Passwords;

class RegistrationModel
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function createUser($values)
    {
        $selection = $this->database->table('user');
        $temp = $selection->where('email = ?', $values->email)->fetch();


        if (!$temp) {
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

        return $temp;
    }
}