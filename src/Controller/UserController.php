<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PassType;
use App\Form\ProfileType;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'user/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profileAction(Request $request, ValidatorInterface $validator)
    {
     $data = $request->request->all();

        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        $errors = $validator->validate($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setFirstName($data['user']['firstName']);
            $user->setLastName($data['user']['lastName']);
            $user->setUsername($data['user']['username']);
            $user->setEmail($data['user']['email']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your profile is successfully updated');
            return $this->redirectToRoute('profile_view');
        }

        return $this->render("user/profile.html.twig", ['user'=>$user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/password", name="change_password")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function changePassword(Request $request)
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $form = $this->createForm(PassType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $passwordFirst = $data['user']['password']['first'];
            $passwordSecond = $data['user']['password']['second'];

            if ($passwordFirst !== $passwordSecond){
                $this->addFlash('error', "Password change failed. The password fields must match. Login again and try again!");
                return $this->redirectToRoute("app_login");
            }
            $password = $this->passwordEncoder->encodePassword(
                $user,
                $passwordFirst
            );
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your password is successfully changed');
            return $this->redirectToRoute('profile_view', ['user'=>$user]);
        }

        $errors = $form->getErrors();

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render("user/password.html.twig", ['user'=>$user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/profile/view", name="profile_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profileView()
    {
        $user = $this->getUser();
        return $this->render('user/userprofile.html.twig', array(
            'user' => $user

        ));
    }

    protected function addFlash(string $type, string $message)
    {
        $flashbag = $this->get('session')->getFlashBag();
        // Add flash message
        $flashbag->add($type, $message);
    }


}
