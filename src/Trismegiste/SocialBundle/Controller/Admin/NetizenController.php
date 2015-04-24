<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\NetizenRoleType;

/**
 * NetizenController is a controller for administrating Netizen
 */
class NetizenController extends Template
{

    /**
     * Gets the repository for netizen
     *
     * @return \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->get('social.netizen.repository');
    }

    public function listingAction(Request $req)
    {
        $it = [];

        if (!(empty($search = $req->query->get('search', '')))) {
            $repo = $this->getRepository();
            $it = $repo->search($search)->limit(5);
        }

        return $this->render('TrismegisteSocialBundle:Admin:Netizen/listing.html.twig', [
                    'listing' => $it
        ]);
    }

    public function promoteAction($id)
    {
        $repo = $this->getRepository();
        $netizen = $repo->findByPk($id);
        $form = $this->createForm(new NetizenRoleType(), $netizen);

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            try {
                $repo->persist($netizen);
                $this->pushFlash('notice', 'User promoted');

                // return to the same page
                return $this->redirectRouteOk('admin_netizen_show', ['id' => $netizen->getId()]);
            } catch (\MongoException $e) {
                $this->pushFlash('warning', 'Cannot promote user');
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:Netizen/edit.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    public function blockWriteUntilAction()
    {
        // @todo blockWriteUntilAction
    }

    public function showAction($id)
    {
        return $this->render('TrismegisteSocialBundle:Admin:Netizen/show.html.twig', [
                    'netizen' => $this->getRepository()->findByPk($id)
        ]);
    }

}
