<?php
/**
 * Created by PhpStorm.
 * User: Claus Perbony
 * Date: 16/05/2018
 * Time: 10:23
 */

namespace App\AdminBundle\Controller;

use App\Entity\Contratacoes;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RelatoriosController extends Controller
{
    //TODO: Criar Controller Base
    protected $em;

    /**
     * UsuariosController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/relatorios/faturamento", name="admin_relatorio_faturamento")
     * @throws DBALException
     */
    public function faturamento(Request $request)
    {
        $exportar = $request->get("exportar");

        $faturamento = $this->em->getRepository(Contratacoes::class)
            ->retornaFaturamento();

        if ($exportar === "pdf") {
            $html = $this->renderView("@Admin/relatorios/relatorio_faturamento.html.twig",
                [
                    'faturamento' => $faturamento,
                ]);

            $dompdf = $this->get('dompdf');
            $dompdf->streamHtml($html, "relatorio_faturamento.pdf");

        } else {

            return $this->render(
                "@Admin/relatorios/faturamento.html.twig",
                [
                    'faturamento' => $faturamento,
                ]);
        }
    }
}