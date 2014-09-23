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
    protected $config = [
        'simplepost' => [
            'form' => 'Trismegiste\SocialBundle\Form\SimplePostType',
            'entity' => 'Trismegiste\Socialist\SimplePost',
            'show' => 'TrismegisteSocialBundle:Content:simplepost_show.html.twig'
        ],
        'status' => [
            'form' => 'Trismegiste\SocialBundle\Form\StatusType',
            'entity' => 'Trismegiste\Socialist\Status',
            'show' => 'TrismegisteSocialBundle:Content:status_show.html.twig'
        ]
    ];

    public function __construct(FormFactoryInterface $ff)
    {
        $this->formFactory = $ff;
    }

    /**
     * Creates a form for creation of a new entity
     *
     * @param string $type the key of the entity
     * @param AuthorInterface $author
     * @param type $postRoute the route to post the form to
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCreateForm($type, AuthorInterface $author, $postRoute)
    {
        $choice = $this->config[$type];
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
     * Gets the fqcn of the AbstractType subclass from a Publishing subclass
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
            if ($pub instanceof $choice['entity']) {
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
