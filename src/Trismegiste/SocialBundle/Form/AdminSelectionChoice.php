<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Form;

use Iterator;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * AdminSelectionChoice is a multi-line selection for admin listing
 */
class AdminSelectionChoice implements ChoiceListInterface
{

    /** @var Iterator */
    protected $listing;

    public function __construct(Iterator $choices)
    {
        $choices->rewind();

        $this->listing = iterator_to_array($choices);
    }

    public function getChoices()
    {
        return array_values($this->listing);
    }

    public function getChoicesForValues(array $values)
    {
        $choices = [];

        foreach ($values as $key) {
            if (array_key_exists($key, $this->listing)) {
                $choices[] = $this->listing[$key];
            }
        }

        return $choices;
    }

    public function getIndicesForChoices(array $choices)
    {
        return $this->getValuesForChoices($choices);
    }

    public function getIndicesForValues(array $values)
    {
        $keys = array_keys($this->listing);

        $indices = [];

        foreach ($values as $value) {
            if ($found = array_search($value, $keys)) {
                $indices[] = $found;
            }
        }

        return $indices;
    }

    public function getPreferredViews()
    {
        return [];
    }

    public function getRemainingViews()
    {
        $checkbox = [];

        foreach ($this->listing as $key => $obj) {
            $checkbox[] = new ChoiceView($key, $key, $obj);
        }

        return $checkbox;
    }

    public function getValues()
    {
        return array_keys($this->listing);
    }

    public function getValuesForChoices(array $choices)
    {
        $values = [];
        foreach ($choices as $choice) {
            if ($found = array_search($choice, $this->listing)) {
                $values[] = $found;
            }
        }

        return $values;
    }

}
