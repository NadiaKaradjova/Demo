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
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();

        $user = $this->getUser();
        if ($user){
            foreach ($books as $book){
                /** @var Book $book */
                $usersCollecion = $book->getUsers();
                if ($usersCollecion->contains($user)){
                    $book->setInPrivateCollection();
                }
            }
        }
        return $this->render('index.html.twig', ['books' => $books]);

    }
}
