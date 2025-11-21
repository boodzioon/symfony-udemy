<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Set video title',
                'data' => 'Example title',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Agree?',
                'mapped' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'MP4 Video',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add a Movie'
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $movie = $event->getData();
            $form = $event->getForm();

            if (!$movie || null === $movie->getId())
            {
                $form->add('created_at', DateType::class, [
                    'label' => 'Set date',
                    'widget' => 'single_text',
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
