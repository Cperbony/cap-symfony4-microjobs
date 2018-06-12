<?php

namespace App\Controller;

use Moip\Auth\BasicAuth;
use Moip\Moip;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagamentoController extends Controller
{
//    const TOKEN_MOIP = "DJIMZM3YCEQ8ZSMCIUVI6OS1ENRP0VYZ";
//    const CHAVE_MOIP = "2TK2U5U8E0DEIQEPRVKSDJKNOBQV2WKXHQNIQUJE";

    /**
     * @Route("/pagamento", name="pagamento")
     */
    public function index()
    {
//        $moip = new Moip(new BasicAuth(self::TOKEN_MOIP, self::CHAVE_MOIP),
//            Moip::ENDPOINT_SANDBOX);
        $moip = $this->get('moip')->getMoip();

        dump($moip); exit;

        return $this->render('pagamento/index.html.twig', [
            'controller_name' => 'PagamentoController',
        ]);
    }
}
