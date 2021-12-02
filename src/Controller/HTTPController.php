<?php

namespace App\Controller;
use App\Entity\Pokemon;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HTTPController extends AbstractController
{
    private $client;
    private $listaPokemon = [];
    private const POKEMON_TOTAL = 5;

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

        $encoders = [new JsonEncoder(), new XmlEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer;

        foreach ($pokemon as $poke) {

            $jsonContent = $serializer->serialize($poke, 'json');
        }

        $response = new Response($jsonContent);

        return $response;

        /*return $this->render('http/httpController.html.twig', [
            'controller_name' => 'HTTPController',
        ]);*/
    }

    public function fetchPokemon(): array
    {
        // Variables:

        $repetirPokemon = true;
        $repetirHabilidad = true;
        $objetos = [];

        // Cogemos dos pokemon aleatorios y los devolvemos:

        $pokemon1 = rand(1, self::POKEMON_TOTAL);
        $pokemon2 = rand(1, self::POKEMON_TOTAL);

        while ($pokemon2 === $pokemon1) {

            $pokemon2 = rand(1, self::POKEMON_TOTAL);
        }

        for ($i = 0; $i < 2 ; $i++) {

            while ($repetirPokemon == true) {

                if ($i == 0) {

                    $response = $this->client->request(
                        'GET',
                        "https://pokeapi.co/api/v2/pokemon/$pokemon1"
                    );

                } else {

                    $response = $this->client->request(
                        'GET',
                        "https://pokeapi.co/api/v2/pokemon/$pokemon2"
                    );
                }
                $statusCode = $response->getStatusCode();
                // $statusCode = 200
                $contentType = $response->getHeaders()['content-type'][0];
                // $contentType = 'application/json'
                $content = $response->getContent();
                // $content = '{"id":521583, "name":"symfony-docs", ...}'
                $content = $response->toArray();
                // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

                $pokemon[$i] = [$content['name'], $content['abilities']];

                $long = count($pokemon[$i][1]);

                while ($repetirHabilidad == true) {

                    $habilidad = rand(0, $long-1);

                    $habilidad = $pokemon[$i][1][$habilidad]['ability']['name'];

                    //$pokemon[$i] = ['pokemon' => $content['name'], 'habilidad' => $habilidad];

                    //Objeto y booleano encontrado:

                    $encontrado = false;

                    foreach ($this->listaPokemon as $pokemonEnLista) {

                        if ($pokemonEnLista->getName() === $content['name']) {

                            $encontrado = true;
                            $pokemonEncontrado = $pokemonEnLista;
                        }
                    }

                    if ($encontrado == false) {

                        $pokemonObjeto = new Pokemon($content['name'], $habilidad);
                        array_push($this->listaPokemon, $pokemonObjeto);
                        $objetos[$i] = [$pokemonObjeto];

                    } else {

                        $habilidadEncontrada = false;

                        foreach ($pokemonEncontrado->getAbilities() as $habilidadEnLista) {

                            if ($habilidadEnLista === $habilidad) {

                                $habilidadEncontrada = true;
                            }
                        }

                        if ($habilidadEncontrada == false) {

                            array_push($pokemonEncontrado->getAbilities(), $habilidad);
                            $objetos[$i] = [$pokemonEncontrado];

                        } else if ($habilidadEncontrada == true && $long === $pokemonEncontrado->getAbilities()) {

                            $repetirPokemon = true;

                        } else if ($habilidadEncontrada == true && $long > $pokemonEncontrado->getAbilities()) {

                            $repetirHabilidad = true;

                        } else {

                            $repetirHabilidad = false;
                            $repetirPokemon = false;
                        }
                    }
                }
            }
        }

        return $objetos;
    }
}
