<?php

namespace App\Controller;

use App\Entity\Servico;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends Controller
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @Route("/", name="default")
     * @Template("default/index.html.twig")
     */
    public function index(Request $request)
    {
        $busca = $request->get('busca');
        $microjobs = $this->em->getRepository(Servico::class)
            ->findByListagem($busca);

        return [
            'microjobs' => $microjobs
        ];
    }


    /**
     * @Route("/painel", name="painel")
     * @Template("default/painel.html.twig")
     * @param UserInterface $user
     * @param Request $request
     * @return array
     */
    public function painel(UserInterface $user, Request $request)
    {
        $status = $request->get("busca_filtro");
        $microjobs = $this->em->getRepository(
            Servico::class)->findByUsuarioAndStatus($user, $status);

        return [
            'microjobs' => $microjobs,
            'status' => $status
        ];
    }

    /**
     * @param Servico $servico
     * @Route("/microjob/{slug}", name="visualizar_job")
     * @Template("default/visualizar-job.html.twig")
     * @return array
     */
    public function visualizar_job(Servico $servico)
    {
        return [
            'job' => $servico
        ];
    }
}
