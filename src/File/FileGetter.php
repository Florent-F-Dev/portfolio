<?php

namespace App\File;

use App\Entity\Post;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FileGetter extends AbstractController
{

    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getFile(FormInterface $form, Post $post = null): string
    {
        $img = $form->get('photo')->getData();
        if ($img) {
            $img = $form->get('photo')->getData();
            $title = $this->slugger->slug(mb_strtolower($form->get('title')->getData()));

            $file = $title . "." . $img->guessExtension();

            $img->move($this->getParameter('image_directory'), $file);
        } else {
            if ($post) {
                if (!stristr($post->getPhoto(), "picsum")) {
                    unlink($this->getParameter('image_directory') . "/" . $post->getPhoto());
                }
            }
            $file = "https://picsum.photos/400/300";
        }
        return $file;
    }
}