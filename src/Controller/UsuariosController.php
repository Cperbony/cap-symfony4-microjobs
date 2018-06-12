<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioDadosPessoaisType;
use App\Form\UsuarioType;
use Doctrine\ORM\EntityManagerInterface;
use Moip\Auth\Connect;
use Moip\Exceptions\UnautorizedException;
use Moip\Exceptions\UnexpectedException;
use Moip\Exceptions\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UsuariosController extends Controller
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/usuarios", name="usuarios")
     */
    public function index()
    {
        return $this->render('usuarios/index.html.twig', [
            'controller_name' => 'UsuariosController',
        ]);
    }

    /**
     * @Route("/usuarios/login", name="login")
     * @Template("usuarios/login.html.twig")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return array
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $user_name = $authUtils->getLastUsername();

        return [
            'last_username' => $user_name,
            'error' => $error
        ];
    }

//    /**
//     * @Route("/painel", name="painel")
//     * @return Response
//     */
//    public function painel()
//    {
//        return new Response("<h1> Painel </h1>");
//    }

    /**
     * @Route("/usuario/cadastrar", name="cadastrar_usuario")
     * @Template("usuarios/registro.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cadastrar(Request $request, \Swift_Mailer $mailer)
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $senha_cript = $encoder->encodePassword($usuario, $form->getData()->getPassword());
            $usuario->setSenha($senha_cript);

            $token = md5(uniqid());
            $usuario->setToken($token);

            $usuario->setRoles("ROLE_FREELA");

            $this->em->persist($usuario);
            $this->em->flush();

            $this->get('email')->enviar(
                $usuario->getNome() . ", ative sua conta no Microjobs",
                [$usuario->getEmail() => $usuario->getNome()],
                "emails/usuarios/registro.html.twig", [
                    'nome' => $usuario->getNome(),
                    'token' => $usuario->getToken()
                ]
            );

//            $mensagem = (new \Swift_Message($usuario->getNome() . "ative sua conta no Microjobs CAP"))
//                ->setFrom('noreply@email.com')
//                ->setTo([$usuario->getEmail() => $usuario->getNome()])
//                ->setBody($this->renderView("emails/usuarios/registro.html.twig", [
//                    'nome' => $usuario->getNome(),
//                    'token' => $usuario->getToken()
//                ]), 'text/html');
//
//            $mailer->send($mensagem);

            $this->addFlash("success", "Verifique seu email para completar seu cadastro!");
            return $this->redirectToRoute("default");
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/usuarios/ativar-conta/{token}", name="email_ativar_conta")
     */
    public function ativarConta($token)
    {
        $usuario = $this->em->getRepository(Usuario::class)->findOneBy(['token' => $token]);
        $usuario->setStatus(true);

        $this->em->persist($usuario);
        $this->em->flush();

        $this->addFlash("success", "Cadastrado ativado com Sucesso! Informe email e senha para acessar o sistema");
        return $this->redirectToRoute("login");
    }

    /**
     * @Route("/painel/usuario/mudar-para-cliente", name="mudar_para_cliente")
     * @Template("usuarios/mudar-para-cliente.html.twig")
     */
    public function mudarParaCliente()
    {

    }

    /**
     * @Route("/painel/usuario/mudar-para-cliente/confirmar", name="confirmar_mudar_para_cliente")
     *
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmarMudarParaCliente(UserInterface $user)
    {
        $usuario = $this->em->getRepository(Usuario::class)->find($user);
        $usuario->limparRoles();

        $usuario->setRoles("ROLE_CLIENTE");

        $this->em->persist($usuario);
        $this->em->flush();

        $this->addFlash("success", "Perfil Alterado com Sucesso!");
        return $this->redirectToRoute("painel");
    }

    /**
     * @Route("/painel/usuario/dados-pessoais", name="dados_pessoais")
     * @Template("usuarios/dados-pessoais.html.twig")
     * @param UserInterface $user
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function dadosPessoais(UserInterface $user, Request $request)
    {
        $usuario = $this->em->getRepository(Usuario::class)->find($user);
        $form = $this->createForm(UsuarioDadosPessoaisType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->getDadosPessoais()->setDataAlteracao(new \DateTime());
            $this->em->persist($usuario);
            $this->em->flush();

            $this->addFlash("success", "Dados Alterado com Sucesso!");
            return $this->redirectToRoute("painel");
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/painel/usuario/auth-moip")
     */
    public function getAuthMoip()
    {
        try {
            $redir_uri = $this->getParameter('moip_url_retorno');
            $app_id = $this->getParameter('moip_app_id');
            $scope = true;
            $connect = new Connect($redir_uri, $app_id, $scope, Connect::ENDPOINT_SANDBOX);
            $connect->setScope(Connect::RECEIVE_FUNDS)
                ->setScope(Connect::REFUND)
                ->setScope(Connect::MANAGE_ACCOUNT_INFO)
                ->setScope(Connect::RETRIEVE_FINANCIAL_INFO);

            return $this->json(['url' => $connect->getAuthUrl()]);


        } catch (UnautorizedException $e) {
            echo $e->getMessage();
        } catch (ValidationException $e) {
            echo $e->getMessage();
        } catch (UnexpectedException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     *
     * @Route("/painel/usuarios/autorizar-moip")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getCodeMoip(Request $request, UserInterface $user)
    {
        $code = $request->get("code");

        try {
            $redir_uri = $this->getParameter('moip_url_retorno');
            $app_id = $this->getParameter('moip_app_id');
            $scope = true;
            $connect = new Connect($redir_uri, $app_id, $scope, Connect::ENDPOINT_SANDBOX);
            $secret = $this->getParameter('moip_secret');
            $connect->setClientSecret($secret);
            $connect->setCode($code);
            $auth = $connect->authorize();

//            dump($auth); exit;

            $usuario = $this->em->getRepository(Usuario::class)->find($user);
            $usuario->getDadosPessoais()->setMoipAccessToken($auth->access_token);
            $usuario->getDadosPessoais()->setMoipIdConta($auth->moipAccount->id);

            $this->em->persist($usuario);
            $this->em->flush();

            $this->addFlash("success", "Sua conta Moip foi vinculada com Sucesso!");
            return $this->redirectToRoute("painel");

        } catch (UnautorizedException $e) {
            echo $e->getMessage();
        } catch (ValidationException $e) {
            echo $e->getMessage();
        } catch (UnexpectedException $e) {
            echo $e->getMessage();
        }
    }
}
