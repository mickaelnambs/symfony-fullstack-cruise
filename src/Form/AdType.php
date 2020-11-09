<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\City;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdType extends ApplicationType
{
    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Titre", "Le titre de votre annonce ..."))
            ->add('price', IntegerType::class, $this->getConfiguration("Prix", "Le prix de votre annonce ..."))
            ->add('place', IntegerType::class, $this->getConfiguration("Nombre de place", "Le nombre de place maximum ..."))
            ->add('images', FileType::class, $this->getConfiguration("Photos", "Les photos ...", ['mapped' => false, 'multiple' => true, 'required' => false]))
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Une petite description ..."))
            ->add('city', EntityType::class, $this->getConfiguration("Trajet", false, ['class' => City::class, 'choice_label' => 'name']))

            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Ad */
                $ad = $event->getData();
                if (null !== $adName = $ad->getName()) {
                    $ad->setSlug($this->slugger->slug($adName)->lower());
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
