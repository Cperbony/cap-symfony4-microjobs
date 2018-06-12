<?php

namespace App\Controller;

use App\Entity\Contratacoes;
use App\Entity\Servico;
use App\Entity\Usuario;
use App\Form\ServicoType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        return $this->render('servicos/index.html.twig', [
            'controller_name' => 'ServicosController',
        ]);
    }

    /**
     * @Route("/painel/servicos/cadastrar", name="cadastrar_job")
     * @Template("servicos/novo-micro-jobs.html.twig")
     * @param Request $request
     * @param UserInterface $user
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
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
     * @Route("/contratar-servico/{id}/{slug}/tela-pagamento", name="tela-pagamento")
     * @Template("servicos/tela-pagamento.html.twig")
     */
    public function telaPagamento(Servico $servico, UserInterface $user)
    {
        if($user->getRoles()[0] == "ROLE_FREELA") {
            $this->addFlash("warning", "<h3>Atenção</h3><p>Para contratar um serviço é necessário ser um cliente.</p><br><p>Acesse seu painel e faça a migração gratuitamente!</p>");
            return $this->redirectToRoute('painel');
        }

        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('numero', TextType::class,[
                'label' => "Número do Cartão"
            ])
            ->add('mes_expiracao', TextType::class, [
                'label' => "Mês"
            ])
            ->add('ano_expiracao', TextType::class, [
                'label' => "Ano"
            ])
            ->add('cod_seguranca', TextType::class, [
                'label' => "Código Segurança"
            ])
            ->add('enviar', ButtonType::class, [
                'label' => "Realizar Pagamento",
                'attr' => [
                    'class' => 'text-center btn btn-primary'
                ]
            ])
            ->getForm();

        return [
            'job' => $servico,
            'form' => $form
        ];
    }

    /**
     * @Route("/contratar-servico/{id}/{slug}/tela-pagamento", name="tela_pagamento")
     * @Template("servicos/tela-pagamento.html.twig")
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
