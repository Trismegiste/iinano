<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

/**
 * Profile contains data from a user. They are non-relevant data for this app,
 * only for humans.
 */
class Profile
{

    public $email;
    public $gender;
    public $fullName;
    public $location;
    public $dateOfBirth;
    public $placeOfBirth;
    public $defaultWallFilter = 'self';
    public $joinedAt;
    public $publishingCounter = 0;
    public $commentaryCounter = 0;

    public function __construct()
    {
        $this->joinedAt = new \DateTime();
    }

}
