<?php

namespace App\Controller;

use App\Entity\Contratacoes;
use App\Entity\Servico;
use App\Entity\Usuario;
use App\Form\ServicoType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;

class ServicosController extends Controller
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/servicos", name="servicos")
     */
    public function index()
    {
        return $this->render('servico/index.html.twig', [
            'controller_name' => 'ServicosController',
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
            $imagem = $servico->getImagem();
            $nome_arquivo = md5(uniqid()) . "." . $imagem->guessExtension();
            $imagem->move(
                $this->getParameter('caminho_img_job'),
                $nome_arquivo
            );
            $servico->setImagem($nome_arquivo);
            $servico->setUsuario($user);

            $servico->setValor(30.00);
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

    /**
     * @Route("/painel/servicos/excluir/{id}", name="excluir_servico")
     * @param Servico $servico
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function excluir(Servico $servico)
    {
        $servico->setStatus("E");
        $this->em->persist($servico);
        $this->em->flush();

        $this->addFlash("success", "Excluído com Sucesso");
        return $this->redirectToRoute('painel');
    }

    /**
     * @Route("/contratar-servico/{id}/slug", name="contratar_servico")
     * @param Servico $servico
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contratarServico(Servico $servico, UserInterface $user)
    {
        //TODO: Verificar acesso somente se Logado

        $contratacao = new Contratacoes();
        $contratacao->setValor($servico->getValor())
            ->setCliente($user)
            ->setFreelancer($servico->getUsuario())
            ->setServico($servico)
            ->setStatus("A");

        $this->em->persist($servico);
        $this->em->flush();

        $this->get('email')->enviar(
        $user->getNome() . ' - Contratação de Serviço',
        [$user->getEmail() => $user->getNome()],
        'emails/servicos/contratacao_cliente.html.twig', [
            'servico' => $servico,
            'cliente' => $user
        ]
    );

        $this->get('email')->enviar(
            $servico->getUsuario()->getNome() . ' - Parabéns',
            [$servico->getUsuario()->getEmail() => $servico->getUsuario()],
            'emails/servicos/contratacao_freelancer.html.twig', [
                'servico' => $servico,
                'cliente' => $user
            ]
        );

        $this->addFlash("success", "Servico Contratado com Sucesso");
        return $this->redirectToRoute('default');
    }

    /**
     * @Route("/painel/servicos/listar-compras", name="listar_compras")
     * @Template("servicos/listar-compras.html.twig")
     * @param UserInterface $user
     * @return array
     */
    public function listarCompras(UserInterface $user)
    {
        $usuario = $this->em->getRepository(Usuario::class)->find($user);
        return [
            'compras' => $usuario->getCompras()
        ];
    }

    /**
     * @Route("/painel/servicos/listar-vendas", name="listar_vendas")
     * @Template("servicos/listar-vendas.html.twig")
     * @param UserInterface $user
     * @return array
     */
    public function listarVendas(UserInterface $user)
    {
        $usuario = $this->em->getRepository(Usuario::class)->find($user);
        return [
            'vendas' => $usuario->getVendas()
        ];
    }
}
