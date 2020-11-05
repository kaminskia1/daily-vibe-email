<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Form\MailType;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @param Request $r
     * @return Response
     */
    public function index(): Response
    {
        $form = $this->createForm(MailType::class);

        return $this->render('default/base.html.twig', [
            'template' => 'form',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", methods={"POST"})
     * @param Request $req
     * @return Response
     */
    public function register(Request $req): Response
    {
        $email = new Mail();
        $form = $this->createForm(MailType::class, $email);

        if ( $req->isMethod("POST"))
        {
            $form->submit($req->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($email);
                $em->flush();

                return $this->redirect($this->generateUrl('submit', ['state'=>'success']));
            }
        }
        return $this->redirect($this->generateUrl('submit', ['state' => 'error']));
    }

    /**
     * @Route("/submit/{state}", methods={"GET", "POST"}, name="submit")
     * @param string $state
     * @return Response
     */
    public function complete(string $state)
    {
        return $this->render('default/base.html.twig', [
            'template' => 'response',
            'response' => $state,
        ]);
    }

}