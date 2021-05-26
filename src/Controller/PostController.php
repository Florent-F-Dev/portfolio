<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\File\FileGetter;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/admin/post")
 * @IsGranted("ROLE_ADMIN", message="Not found")
 */
class PostController extends AbstractController
{

    protected $postRepository;
    protected $slugger;
    protected $fileGetter;

    public function __construct(PostRepository $postRepository, SluggerInterface $slugger, FileGetter $fileGetter)
    {
        $this->postRepository = $postRepository;
        $this->slugger = $slugger;
        $this->fileGetter = $fileGetter;
    }

    /**
     * @Route("/add", name="post_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $post = new Post;

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $this->fileGetter->getFile($form);
            $post->setPhoto($file);
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
     * @Route("/edit/{id}", name="post_edit", requirements={"id": "\d+"})
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

            $file = $this->fileGetter->getFile($form, $post);
            $post->setPhoto($file);
            $em->flush();

            return $this->redirectToRoute('post');
        }

        $formView = $form->createView();

        return $this->render('post/edit.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/list", name="post_list")
     */
    public function list()
    {
        $posts = $this->postRepository->findAll();

        return $this->render("post/list.html.twig", [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/delete/{id}", name="post_delete")
     */
    public function delete($id, EntityManagerInterface $em)
    {
        $post = $this->postRepository->find($id);
        if (!stristr($post->getPhoto(), "picsum")) {
            unlink($this->getParameter('image_directory') . "/" . $post->getPhoto());
        }
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('post_list');
    }
}