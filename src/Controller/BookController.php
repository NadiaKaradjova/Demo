<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BookController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @Route("/book/create", name="book_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createBook(Request $request, FileUploader $fileUploader): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $cover = $form['coverImage']->getData();
            if ($cover) {
                $coverFileName = $fileUploader->upload($cover);
                $book->setCoverImage($coverFileName);
            }

            $this->getDoctrine()->getManager()->persist($book);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('book/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/book/{id}", name="book_view")
     * @param $id
     * @return Response
     */
    public function viewArticle($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        return $this->render('book/book.html.twig', ['book' => $book]);
    }

    /**
     * @Route("book/edit/{id}", name="article_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editArticle($id, Request $request)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        if ($book === null){
            return $this->redirectToRoute("blog_index");
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($book) && !$currentUser->isAdmin()){
            return $this->redirectToRoute("index");
        }

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest(($request));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('book_view', array('id' => $book->getId()));
        }

        return $this->render('book/edit.html.twig', array('book' => $book, 'form' => $form->createView()));
    }

    /**
     *
     * @Route("book/delete/{id}", name="book_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id, Request $request)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        if ($book === null){
            return $this->redirectToRoute("index");
        }

        $currentUser = $this->getUser();
        if (!$currentUser->isAuthor($book) && !$currentUser->isAdmin()){
            return $this->redirectToRoute("index");
        }

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest(($request));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($book);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('book/delete.html.twig', array('book' => $book, 'form' => $form->createView()));
    }
}
