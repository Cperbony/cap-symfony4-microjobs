<?php
/**
 * Created by PhpStorm.
 * User: Claus Perbony
 * Date: 15/05/2018
 * Time: 12:20
 */

namespace App\AdminBundle\Controller;

use App\Entity\Servico;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServicosController extends Controller
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/listar-jobs", name="admin_listar_jobs")
     * @Template("@Admin/servicos/listar-jobs.html.twig")
     */
    public function listarJobs(Request $request)
    {
        $status = $request->get('busca_filtro');

        $jobs = $this->em->getRepository(Servico::class)->findByUsuarioAndStatus(null, $status);

        return [
            'status' => $status,
            'microjobs' => $jobs
        ];
    }

    /**
     * @Route("/publicar-job/{id}", name="admin_publicar_job")
     * @param Servico $servico
     * @return JsonResponse
     */
    public function publicarJob(Servico $servico)
    {
        $servico->setStatus("P");
        $this->em->persist($servico);
        $this->em->flush();

        return new JsonResponse(
            [
                'success' => true,
                [
                    'servico' => $servico->getId()
                ]
            ]);
    }

    /**
     * @Route("/rejeitar-job/{id}", name="admin_rejeitar_job")
     * @param Servico $servico
     * @return JsonResponse
     */
    public function rejeitarJob(Servico $servico)
    {
        $servico->setStatus("R");
        $this->em->persist($servico);
        $this->em->flush();

        return new JsonResponse(
            [
                'success' => true,
                [
                    'servico' => $servico->getId()
                ]
            ]);
    }


}