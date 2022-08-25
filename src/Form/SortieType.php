<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true, 'label' => 'Nom de la sortie : '
            ])
            ->add('limitDate', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription : '
            ])
            ->add('eventDate', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : ',
                'invalid_message'=>'Le nombre de place doit être supérieur à 0',
            ])
            ->add('nbrPlace', IntegerType::class, [
                'required' => true,
                'label' => 'Nombre de places : ',
                'attr'=>['step'=>1, 'min'=>1,"pattern"=>"\d+" ]
            ])
            ->add('duration', TimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Durée: '
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Description et infos : '
            ])
            ->add('campus', EntityType::class, [
                'required' => true,
                'class' => Campus::class,
                'choice_label' => 'name'
            ])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => Ville::class,
                'choice_label' => 'name'
            ])
            /* ->add('place', PlaceType::class, [
                'required' => true,
                'label' => ' '
            ]) */
            ->add('register', SubmitType::class, [
                'attr' => ['value' => 1],
                'label' => 'Enregistrer'
            ])
            ->add('publish', SubmitType::class,
                ['attr' => ['value' => 2],
                    'label' => 'Publier la sortie'
                ])
//            ->add('cancel', ResetType::class, [
//                'label' => 'Annuler',
//                'attr'=>['type'=>'reset']
//            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
