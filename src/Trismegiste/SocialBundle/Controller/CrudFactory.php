<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\Socialist\Publishing;
use Trismegiste\Socialist\AuthorInterface;
use Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * CrudFactory is a (abstract ?) factory for the CRUD operations
 * on Publishing content subclasses
 */
class CrudFactory
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    /* @todo inject this config from social config into the ctor */

    /** @var array */
    protected $config = [];

    /**
     * Ctor
     *
     * @param FormFactoryInterface $ff
     * @param string $typeNamespace the namespace of form Type
     * @param array $contentAlias an array of content aliases
     */
    public function __construct(FormFactoryInterface $ff, $typeNamespace, array $contentAlias)
    {
        $this->formFactory = $ff;
        
        foreach ($contentAlias as $key => $fqcn) {
            preg_match('#([^\\\\]+)$#', $fqcn, $extract);
            $this->config[$key] = [
                'form' => $typeNamespace . '\\' . $extract[1] . 'Type',
                'entity' => $fqcn
            ];
        }
    }

    /**
     * Creates a form for creation of a new entity
     *
     * @param string $alias the alias of the entity
     * @param AuthorInterface $author
     * @param type $postRoute the route to post the form to
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCreateForm($alias, AuthorInterface $author, $postRoute)
    {
        $choice = $this->config[$alias];
        $refl = new \ReflectionClass($choice['entity']);
        $publish = $refl->newInstance($author);
        $typeClass = $choice['form'];

        return $this->formFactory->create(new $typeClass
                        , $publish, ['action' => $postRoute]);
    }

    /**
     * Creates a form for edition of an existing entity
     *
     * @param Publishing $publish the entity to edit
     * @param type $postRoute the route to post the form to
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createEditForm(Publishing $publish, $postRoute)
    {
        return $this->formFactory->create($this->createTypeFromPublishing($publish)
                        , $publish, ['action' => $postRoute]
        );
    }

    /**
     * Gets the fqcn of the AbstractType subclass from a given Publishing subclass
     *
     * @param Publishing $pub the entity
     *
     * @return string FQCN of type
     *
     * @throws \InvalidArgumentException if the entity is not registered
     */
    private function getTypeFromPublishing(Publishing $pub)
    {
        foreach ($this->config as $choice) {
            if (get_class($pub) === $choice['entity']) {
                return $choice['form'];
            }
        }

        throw new \LogicException(get_class($pub) . " is not registered");
    }

    protected function createTypeFromPublishing(Publishing $pub)
    {
        $typeClass = $this->getTypeFromPublishing($pub);

        return new $typeClass;
    }

}
