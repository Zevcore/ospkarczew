<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route("/", name: "app_index")]
    public function index(): Response
    {
        $postsUrl = realpath(__DIR__ . '/../../data/posts.json');
        $facebookData = json_decode(file_get_contents($postsUrl), true);

        return $this->render('app/app.html.twig',[
            'facebookData' => $facebookData
        ]);
    }

}