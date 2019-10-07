<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Cover;
use App\Entity\User;
use App\Form\BookType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class BookController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @Route("/book/create", name="book_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($file = $request->files->get('coverImage')) {
                try {
                    $mimeTypes = ["image/gif", "image/png", "image/jpeg", "image/bmp"];
                    if (in_array($file->getMimeType(), $mimeTypes)) {
                        $cover = new Cover();
                        $cover->setFile($request->files->get('coverImage'));
                        $this->getDoctrine()->getManager()->persist($cover);
                        $this->getDoctrine()->getManager()->flush();

                        $book->setCoverImage($cover);
                    } else {
                        $this->addFlash('error', "This type of file is not supported. Please upload a valid picture - .jpg, .png, .bmp or .gif");
                        return $this->render('book/create.html.twig', array('form' => $form->createView()));
                    }

                } catch (BadRequestHttpException $exception) {
                    $this->addFlash('error', "This type of file is not supported. Please upload a valid picture - .jpg, .png, .bmp or .gif");
                   return $this->render('book/create.html.twig', array('form' => $form->createView()));
                }
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
    public function viewBook($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        return $this->render('book/book.html.twig', ['book' => $book]);
    }

    /**
     * @Route("book/edit/{id}", name="book_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editBook($id, Request $request)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if ($book === null) {
            return $this->redirectToRoute("index");
        }

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest(($request));

        if ($form->isSubmitted() && $form->isValid()) {

            if ($file = $request->files->get('coverImage')) {
                try {
                    $mimeTypes = ["image/gif", "image/png", "image/jpeg", "image/bmp"];
                    if (in_array($file->getMimeType(), $mimeTypes)) {

                        /** @var Cover $cover */
                        $cover = $book->getCoverImage();

                        $cover->setFile($request->files->get('coverImage'));
                        $this->getDoctrine()->getManager()->persist($cover);
                        $this->getDoctrine()->getManager()->flush();

                        $book->setCoverImage($cover);
                    } else {
                        $this->addFlash('error', "This type of file is not supported. Please upload a valid picture - .jpg, .png, .bmp or .gif");
                        return $this->render('book/edit.html.twig', array('book' => $book, 'form' => $form->createView()));
                    }

                } catch (BadRequestHttpException $exception) {
                    $this->addFlash('error', "This type of file is not supported. Please upload a valid picture - .jpg, .png, .bmp or .gif");
                    return $this->render('book/edit.html.twig', array('book' => $book, 'form' => $form->createView()));
                }
            }

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

        if ($book === null) {
            return $this->redirectToRoute("index");
        }

        $defaultData = ['message' => ''];

        $form = $this->createFormBuilder($defaultData)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest(($request));

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->remove($book);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('book/delete.html.twig', array('book' => $book, 'form' => $form->createView()));
    }

    /**
     *
     * @Route("mybooks", name="my_book_collection")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getUserBookCollection()
    {
        $user = $this->getUser();
        /** @var User $user */
        $books = $user->getBookCollection();
        return $this->render('book/my_collection.html.twig', ['books' => $books, "collection" => true]);
    }

    /**
     *
     * @Route("add", name="add_book")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addBookToUserCollection(Request $request)
    {
        $bookId = $request->query->get('bookId');

        $book = $this->getDoctrine()->getRepository(Book::class)->find($bookId);
        $user = $this->getUser();
        /** @var User $user */
        /** @var Book $book */
        $user->addBookCollection($book);
        $book->addUsers($user);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($book);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('index');
    }

    /**
     *
     * @Route("remove", name="remove_book")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function removeBookFromUserCollection(Request $request)
    {
        $bookId = $request->query->get('bookId');

        $book = $this->getDoctrine()->getRepository(Book::class)->find($bookId);
        $user = $this->getUser();
        /** @var User $user */
        /** @var Book $book */
        $user->removeBookCollection($book);
        $book->removeUsers($user);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($book);
        $this->getDoctrine()->getManager()->flush();

        if ($request->query->get('collection')) {
            return $this->redirectToRoute('my_book_collection');
        }
        return $this->redirectToRoute('index');
    }


}
