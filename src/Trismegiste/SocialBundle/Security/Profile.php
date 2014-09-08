<?php

/*
 * Iinano
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
    public $city;
    public $dateOfBirth;
    public $placeOfBirth;
    public $defaultWallFilter = 'self';

}