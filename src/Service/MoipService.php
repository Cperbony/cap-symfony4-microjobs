<?php
/**
 * Created by PhpStorm.
 * User: Claus Perbony
 * Date: 11/06/2018
 * Time: 13:22
 */

namespace App\Service;


use Moip\Auth\OAuth;
use Moip\Moip;

class MoipService
{
    private $access_token;
    private $ambiente;

    public function __construct($access_token, $ambiente = 'dev')
    {
        $ambiente = $ambiente == 'dev' ? Moip::ENDPOINT_SANDBOX : Moip::ENDPOINT_PRODUCTION;

        $this->access_token = $access_token;
        $this->ambiente = $ambiente;
    }

    /**
     * @return string
     */
    public function getMoip()
    {
        return new Moip(new OAuth($this->access_token), $this->ambiente);
    }
    /**
     * @param mixed $access_token
     * @return MoipService
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        return $this;
    }

    /**
     * @param string $ambiente
     * @return MoipService
     */
    public function setAmbiente(string $ambiente): MoipService
    {
        $this->ambiente = $ambiente;
        return $this;
    }



}