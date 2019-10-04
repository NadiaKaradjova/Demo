<?php

namespace App\Controller;

use App\Entity\Book;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     */
    public function indexAction()
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();

        foreach ($books as $book){
            /** @var Book $book */
            $usersCollecion = $book->getUsers();
            if ($usersCollecion->contains($user)){
                $book->setInPrivateCollection();
            }
        }

        return $this->render('index.html.twig', ['books' => $books]);

    }
}
