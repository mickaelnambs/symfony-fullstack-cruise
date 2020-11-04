<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Class ApplicationType.
 */
class ApplicationType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un form.
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * 
     * @return array
     */
    public function getConfiguration(string $label, string $placeholder, array $options = []): array
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }
}