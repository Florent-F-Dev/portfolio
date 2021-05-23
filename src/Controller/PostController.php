<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends AbstractController
{

    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/", name="post")
     */
    public function index()
    {
        $posts = $this->postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/admin/add", name="post_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $post = new Post;

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post');
        }

        $formView = $form->createView();

        return $this->render('post/add.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/edit/{id}", name="post_edit", requirements={"id": "\d+"})
     */
    public function edit($id, Request $request, EntityManagerInterface $em)
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            throw new NotFoundHttpException("Ce post n'existe pas");
        }

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('post');
        }

        $formView = $form->createView();

        return $this->render('post/edit.html.twig', [
            'formView' => $formView
        ]);
    }
}