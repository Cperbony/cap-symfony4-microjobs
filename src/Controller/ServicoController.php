<?php

namespace App\Controller;

use App\Entity\Servico;
use App\Form\ServicoType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;

class ServicoController extends Controller
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/servico", name="servico")
     */
    public function index()
    {
        return $this->render('servico/index.html.twig', [
            'controller_name' => 'ServicoController',
        ]);
    }

    /**
     * @Route("/painel/servicos/cadastrar", name="cadastrar_job")
     * @Template("servicos/novo-micro-jobs.html.twig")
     * @param Request $request
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cadastrar(Request $request, UserInterface $user)
    {
        $servico = new Servico();
        $form = $this->createForm(ServicoType::class, $servico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $servico->setImagem(uniqid() . ".jpg");
            $servico->setValor(30.00);
            $servico->setUsuario($user);
            $servico->setStatus("A"); //Análise

            $this->em->persist($servico);
            $this->em->flush();

            $this->addFlash("success", "Cadastrado com Sucesso");
            return $this->redirectToRoute('painel');

        }

        return [
            'form' => $form->createView()
        ];
    }
}
