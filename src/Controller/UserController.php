<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->passwordEncoder->encodePassword(
                  $user,
                  $user->getPassword()
                );

            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'user/register.html.twig',
            array('form' => $form->createView())
        );
    }


//
//    /**
//     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
//     * @Route("/profile", name="user_profile")
//     */
//    public function profileAction()
//    {
//        $user = $this->getUser();
//        return $this->render("user/profile.html.twig", ['user'=>$user]);
//    }
}
