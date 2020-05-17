<?php

namespace Tests\Controller;

use Swift_Message;
use Tests\ApiTestCase;
use App\Model\RegisterModel;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends ApiTestCase
{
    public function testLoginBadCredencial(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"baduser","password":"badpassword"}'
        );

        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED,
            $client->getResponse()->getStatusCode(),
            'can connect with bad credential'
        );
    }

    public function testLoginAdmin(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"766af668-0c19-4624-bcb4-bdb09ce4dada","password":"default"}'
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            'cannot connect with user credential'
        );
    }

    public function testLoginUser(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"0b819649-bef4-4fb9-a6b4-7b7b0b69961c","password":"default"}'
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            'cannot connect with user credential'
        );
    }
}
