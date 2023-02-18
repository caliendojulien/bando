<?php

namespace App\Services;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
class CheckPassword
{

    #[SecurityAssert\UserPassword(
        message:'Mauvais mot de passe.'
    )]
    protected $oldPassword;

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }







}