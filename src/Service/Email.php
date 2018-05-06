<?php
/**
 * Created by PhpStorm.
 * User: Claus Perbony
 * Date: 04/05/2018
 * Time: 23:27
 */

namespace App\Service;


class Email
{
    public $mailer;
    public $view;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $view)
    {
        $this->mailer = $mailer;
        $this->view = $view;
    }

    public function enviar(string $assunto, array $destinatario, string $template, array $params)
    {
        try {
            $mensagem = (new \Swift_Message($assunto))
                ->setFrom('noreply@email.com')
                ->setTo($destinatario)
                ->setBody($this->view->render($template, $params), 'text/html');
        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        }

        $this->mailer->send($mensagem);
    }

}