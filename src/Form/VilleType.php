<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('codePostal')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }

    /**
     * @param NameFilter $villeFilter
     * @return Ville[]
     */
    public function findName(NameFilter $villeFilter): array
    {
        $query = $this->createQueryBuilder('ville')
            ->andWhere('ville.name LIKE :text')
            ->setParameter('text',"%{$villeFilter->text}%" );

        return $query->getQuery()->getResult();
    }
}
