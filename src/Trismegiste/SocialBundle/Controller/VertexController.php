<?php

/*
 * GraphRpg
 */

namespace Trismegiste\SocialBundle\Controller;

use Trismegiste\SocialBundle\Model\Vertex;
use Trismegiste\SocialBundle\Form\Vertex as VertexForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Trismegiste\SocialBundle\Utils\Helper;

/**
 * VertexController manages CRUD for Vertex
 */
class VertexController extends Template
{

    protected function getVertex($id)
    {
        return $this->getRepo()->findByPk($id);
    }

    public function indexAction()
    {
        $vertex = [];



        return $this->render('TrismegisteSocialBundle:Vertex:index.html.twig', ['vertex' => $vertex]);
    }

    protected function pushHistoryStack(Vertex $v)
    {
        return $this->get('session')
                        ->getBag('history')
                        ->push($this->generateUrl('vertex_show', ['id' => $v->getId()]), $v->getTitle());
    }

    public function showAction($id)
    {
        $vertex = $this->getVertex($id);
        $this->pushHistoryStack($vertex);

        return $this->render('TrismegisteFrontBundle:Vertex:show.html.twig', ['vertex' => $vertex]);
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new VertexForm());

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $vertex = $form->getData();
                $vertex->setGraphId($this->getWorkingDoc()->getId());
                $this->getRepo()->persist($vertex);
                $this->pushFlash('notice', 'Created');

                return $this->redirectRouteOk('vertex_edit', ['id' => $vertex->getId()]);
            } else {
                $this->pushFlash('warning', 'Invalid');
            }
        }

        return $this->render('TrismegisteFrontBundle:Vertex:create.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    public function editAction($id, Request $request)
    {
        $vertex = $this->getVertex($id);
        $form = $this->createForm(new VertexForm(), $vertex);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->getRepo()->persist($vertex);
                $this->pushFlash('notice', 'Updated');

                return $this->redirectRouteOk('vertex_edit', ['id' => $vertex->getId()]);
            } else {
                $this->pushFlash('warning', 'Invalid');
            }
        }

        return $this->render('TrismegisteFrontBundle:Vertex:edit.html.twig', [
                    'form' => $form->createView(),
                    'vertex' => $vertex
        ]);
    }

    public function findSlugAction($slug)
    {
        $vertex = $this->getRepo()->findSlugInGraph($this->getGraphFilter(), $slug);

        if (is_null($vertex)) {
            $vertex = new Vertex('undefined');
            $vertex->setTitle(Helper::slugToReadable($slug));
            $form = $this->createForm(new VertexForm(), $vertex);
            $this->pushFlash('warning', "$slug does not exist, would you like to create it ?");

            return $this->render('TrismegisteFrontBundle:Vertex:create.html.twig', [
                        'vertex' => $vertex,
                        'form' => $form->createView()
            ]);
        } else {
            $this->pushHistoryStack($vertex);

            return $this->render('TrismegisteFrontBundle:Vertex:show.html.twig', ['vertex' => $vertex]);
        }
    }

    public function getAllMentionAction()
    {
        $found = $this->getRepo()->getMentionByGraph($this->getGraphFilter());

        return new JsonResponse(['users' => $found]);
    }

    public function deleteAction($id)
    {
        $vertex = $this->getCollection()->remove(['_id' => new \MongoId($id)]);
        $this->pushFlash('notice', 'Vertex deleted');

        return $this->redirectRouteOk('trismegiste_homepage');
    }

    public function searchAction(Request $request)
    {
        $keyword = $request->query->get('keyword');
        $cursor = $this->getRepo()->searchTextInGraph($this->getGraphFilter(), $keyword);

        $vertex = [];
        foreach ($cursor as $doc) {
            $vertex[$doc->getInfoType()][] = $doc;
        }

        return $this->render('TrismegisteFrontBundle:Vertex:index.html.twig', ['vertex' => $vertex]);
    }

    public function getBackLinkAction(Vertex $vertex)
    {
        $backlink = $this->getRepo()->searchMentionInGraph($this->getGraphFilter(), $vertex);

        return $this->render('TrismegisteFrontBundle:Vertex:backlink.html.twig', ['backlink' => $backlink]);
    }

}