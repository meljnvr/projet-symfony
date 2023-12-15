<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
    
        $crawler = $client->request("GET","/ad/new");
        
        $this->assertResponseStatusCodeSame(302);

        $crawler = $client->followRedirect();

        $buttonCrawlerNode = $crawler->selectButton('user-login');
        $form = $buttonCrawlerNode->form();
        
        $client->submit($form, [
            'email' => 'popopo@gmail.com',
            'password' => 'glzelzg',
        ]);

        $this->assertResponseStatusCodeSame(302);

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.alert', 'Identifiants invalides.');

        // $client->followRedirect();

        // $this->assertResponseIsSuccessful();

        // $this->assertSelectorTextContains('.flash-success', 'Annonce bien ajoutée');
    }
}
