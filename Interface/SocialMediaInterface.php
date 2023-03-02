<?php

namespace Modules\SocialMediaAuthentication\Interface;

interface SocialMediaInterface
{
    public function responseMessage($responseData, $statusCode);

    public function findProvider($condition);

    public function findUser($condition);

    public function createUser($data);

    public function createProvider($data);

    public function getUserToken($findUser,$provider,$driver,$user);

}
