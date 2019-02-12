<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Article::class);
        $articles = $repository->findBy(
            [],
            [
                'publicationDate' => 'DESC'
            ],
            5
        );
        
        return $this->render(
            'index/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }
}
