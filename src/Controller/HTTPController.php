<?php

namespace App\Controller;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HTTPController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/pokeapi", name="pokeapi")
     */
    public function index(): Response
    {
        $pokemon = $this->fetchPokemon();

        echo var_dump($pokemon);

        return $this->render('http/httpController.html.twig', [
            'controller_name' => 'HTTPController',
        ]);
    }

    public function fetchPokemon(): array
    {
        $response = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/pokemon/ditto'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }
}
