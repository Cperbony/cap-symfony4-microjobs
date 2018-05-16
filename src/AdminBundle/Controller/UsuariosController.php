<?php
/**
 * Created by PhpStorm.
 * User: Claus Perbony
 * Date: 15/05/2018
 * Time: 23:58
 */

namespace App\AdminBundle\Controller;


use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UsuariosController extends Controller
{
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
     * @Route("/usuarios/listar", name="admin_listar_usuarios")
     * @Template("@Admin/usuarios/listar.html.twig")
     * @param Request $request
     * @return array
     */
    public function listar(Request $request)
    {
        $status = $request->get('status');

        if ($status === "" || is_null($status)) {
            $usuarios = $this->em->getRepository(Usuario::class)->findAll();
        } else {
            $usuarios = $this->em->getRepository(Usuario::class)->findBy([
                'status' => $status,
            ]);
        }

        return [
            'usuarios' => $usuarios,
            'status' => $status,

        ];
    }
}