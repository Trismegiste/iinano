<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Ticket;

/**
 * Ticket is a entrance ticket. Acquired with a EntranceFee or a Coupon
 * Conceptually, in an e-commerce, this is an order
 */
class Ticket implements EntranceAccess
{

    /** @var PurchaseChoice */
    protected $purchase;

    /** @var \DateTime */
    protected $purchasedAt;

    public function __construct(PurchaseChoice $purchaseSystem, \DateTime $now = null)
    {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        $this->purchase = $purchaseSystem;
        $this->purchasedAt = $now;
    }

    /**
     * @inheritdoc
     */
    public function isValid(\DateTime $now = null)
    {
        if (is_null($now)) {
            $now = new \DateTime();
        }

        return $this->getExpiredAt()->getTimestamp() >= $now->getTimestamp();
    }

    /**
     * @inheritdoc
     */
    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

    /**
     * @inheritdoc
     */
    public function getExpiredAt()
    {
        $tmp = clone $this->purchasedAt;
        $tmp->modify($this->purchase->getDuration());

        return $tmp;
    }

}

/* @todo Statistique & predction pour les tickets
 *
 * * 1ere stat :
 * Pour chaque ticket valide en cours
 *  - on ajoute +1 le jour/mois d'achat
 *  - on ajoute -1 le jour/mois d'expiration (dans un an)
 *  - pour les inscriptions futures, on prend la moyenne sur l'année précédente
 *  - on peut projeter les inscriptions (payantes) en cours
 *  - on encadre la courbe avec la moyenne par 2 autres courbes :
 *      - une sans inscription
 *      - une avec le max d'inscription sur un mois (?) de l'année précédente
 *
 * * 2e stat
 * Taux de renouvellement
 */