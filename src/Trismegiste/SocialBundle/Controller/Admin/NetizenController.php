<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Controller\Admin;

use MongoException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trismegiste\SocialBundle\Controller\Template;
use Trismegiste\SocialBundle\Form\NetizenFilterType;
use Trismegiste\SocialBundle\Form\NetizenRoleType;
use Trismegiste\SocialBundle\Form\TicketType;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * NetizenController is a controller for administrating Netizen
 */
class NetizenController extends Template
{

    /**
     * Gets the repository for netizen
     *
     * @return NetizenRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->get('social.netizen.repository');
    }

    public function listingAction(Request $req)
    {
        $it = [];
        $repo = $this->getRepository();
        $filter = $this->createForm(new NetizenFilterType($this->container->getParameter('nickname_regex')));

        $filter->handleRequest($req);
        if ($filter->isValid()) {
            $it = $repo->search($filter->getData());

            $exportAction = $filter->get('export')->isClicked();
            if (!$exportAction) {
                $it->limit(100);
            }
        }

        return $this->render('TrismegisteSocialBundle:Admin:Netizen/listing.html.twig', [
                    'listing' => $it,
                    'filter' => $filter->createView()
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
                $repo->promote($netizen, $this->get('security.context'));
                $this->pushFlash('notice', 'User promoted');

                // return to the same page
                return $this->redirectRouteOk('admin_netizen_show', ['id' => $netizen->getId()]);
            } catch (MongoException $e) {
                $this->pushFlash('warning', 'Cannot promote user');
            } catch (AccessDeniedException $e) {
                $this->pushFlash('warning', $e->getMessage());
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

    /**
     * Edit last ticket of a Netizen
     */
    public function editTicketAction($id)
    {
        $repo = $this->getRepository();
        /** @var Netizen */
        $netizen = $repo->findByPk($id);
        $ticket = $netizen->getLastTicket();

        $form = $this->createForm(new TicketType(), $ticket);

        return $this->render('TrismegisteSocialBundle:Admin:Netizen/edit.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
