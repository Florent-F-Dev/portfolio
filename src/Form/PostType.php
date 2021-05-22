<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du site',
                'attr' => [
                    'placeholder' => 'Titre du site'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du site',
                'attr' => [
                    'placeholder' => 'Description du site'
                ]
            ])
            ->add('link', UrlType::class, [
                'label' => 'Url du site',
                'attr' => [
                    'placeholder' => 'Url du site'
                ]
            ])
            ->add('photo', UrlType::class, [
                'label' => 'Url de la photo',
                'attr' => [
                    'placeholder' => 'Url de la photo'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}