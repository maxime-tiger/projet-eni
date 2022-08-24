<?php

use App\Entity\Campus;
use App\Entity\Etat;
use App\Filters\Filters;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, [
                'label' => 'Le nom de la sortie contient: ',
                'required' => false,
                'attr' => ['placeholder' => 'Search'
                ]])
            ->add('campus', EntityType::class, [
                'label' => false,
                'choice_label' => 'nom',
                'placeholder' => 'Campus',
                'required' => false,
                'class' => Campus::class,
                'expanded'=>false,
                'multiple'=>false,
            ])
            ->add('organizer', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.trice',
                'required' => false,
            ])
            ->add('subscribed', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e',
                'required' => false,
                'disabled' => true,
            ])
            ->add('notSubscribed', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e',
                'required' => false,
                'disabled' => true,
            ])
            ->add('passedEvents', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
            ->add('dateStart', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('dateEnd', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filters::class,
            'method'=>'GET',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix(): string
    {
        return '';
    }
}