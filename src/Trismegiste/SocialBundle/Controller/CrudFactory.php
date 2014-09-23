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

    /** @var RouterInterface */
    protected $router;

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

    public function __construct(FormFactoryInterface $ff, RouterInterface $router)
    {
        $this->formFactory = $ff;
        $this->router = $router;
    }

    /**
     *
     * @param string $type
     * @param AuthorInterface $author
     * @param type $postRoute
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
                        , $publish
                        , ['action' => $this->router->generate($postRoute)]);
    }

    public function createEditForm(Publishing $publish, $postRoute)
    {
        return $this->formFactory->create($this->createTypeFromPublishing($publish)
                        , $publish
                        , ['action' => $this->router->generate($postRoute)]
        );
    }

    private function getTypeFromPublishing(Publishing $pub)
    {
        foreach ($this->config as $choice) {
            if ($pub instanceof $choice['entity']) {
                return $choice['form'];
            }
        }
    }

    protected function createTypeFromPublishing(Publishing $pub)
    {
        $typeClass = $this->getTypeFromPublishing($pub);

        return new $typeClass;
    }

}
