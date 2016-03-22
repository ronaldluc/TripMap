<?php
/**
 * @author Ronald Luc
 */

namespace App\Models;

use Nette,
    Nette\Security as NS;

class AuthenticatorModel implements NS\IAuthenticator
{
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $row = $this->database->table('user')
            ->where('username', $username)->fetch();

        if (!$row) {
            throw new NS\AuthenticationException('Uživatel nenalezen.');
        }


        if (!NS\Passwords::verify($password, $row->password)) {
            throw new NS\AuthenticationException('Špatné heslo.');
        }

        if($row->checked == 0) {
            throw new NS\AuthenticationException('Neaktivovaný účet');
        }

        return new NS\Identity($row->id, $row->role, array('username' => $row->username));
    }
}