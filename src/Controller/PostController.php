<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{

    protected $postRepository;
    protected $slugger;

    public function __construct(PostRepository $postRepository, SluggerInterface $slugger)
    {
        $this->postRepository = $postRepository;
        $this->slugger = $slugger;
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
     * @Route("/admin/post/add", name="post_add")
     * @IsGranted("ROLE_ADMIN", message="Not found")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $post = new Post;

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('photo')->getData();
            if ($img) {
                $img = $form->get('photo')->getData();
                $title = $this->slugger->slug(mb_strtolower($form->get('title')->getData()));

                $file = $title . "." . $img->guessExtension();

                $img->move($this->getParameter('image_directory'), $file);
            } else {
                $file = "https://picsum.photos/400/300";
            }
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
     * @Route("/admin/post/edit/{id}", name="post_edit", requirements={"id": "\d+"})
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
            $img = $form->get('photo')->getData();
            if ($img) {
                $img = $form->get('photo')->getData();
                $title = $this->slugger->slug(mb_strtolower($form->get('title')->getData()));

                $file = $title . "." . $img->guessExtension();

                $img->move($this->getParameter('image_directory'), $file);
            } else {
                if (!stristr($post->getPhoto(), "picsum")) {
                    unlink($this->getParameter('image_directory') . "/" . $post->getPhoto());
                }
                $file = "https://picsum.photos/400/300";
            }
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
     * @Route("/admin/post/list", name="post_list")
     */
    public function list()
    {
        $posts = $this->postRepository->findAll();

        return $this->render("post/list.html.twig", [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/admin/post/delete/{id}", name="post_delete")
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