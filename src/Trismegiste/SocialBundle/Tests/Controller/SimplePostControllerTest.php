<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

/**
 * SimplePostControllerTest tests SimplePostController
 */
class SimplePostControllerTest extends WebTestCasePlus
{

    public function testCreateFirstPost()
    {
        $this->client->followRedirects();
        $this->logIn('kirk');

        $crawler = $this->getPage('content_index');
        $link = $crawler->selectLink('Simple Post')->link();
        $crawler = $this->client->click($link);
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['simple_post' => ['title' => __CLASS__, 'body' => __METHOD__]]);
    }

}