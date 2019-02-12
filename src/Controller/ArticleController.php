<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(Article $article, Request $request)
    {
        /*
         * Sous l'article
         * + si l'utilisateur n'est pas connecté,
         *      + l'inviter à le faire pour pouvoir écrire un commentaire
         *      + Sinon, lui afficher un formulaire avec un textarea
         *          pour pouvoir écrire un commentaire.
         * + Nécessite une entité Comment :
         *      + content (text en bdd)
         *      + date de publication (datetime)
         *      + user (l'utilisateur qui écrit le commentaire) ManyToOne
         *      + article (l'article sur lequel on écrit le commentaire) ManyToOne
         * + Nécessite le form type qui va avec contenant le textarea,
         *      + le contenu du commentaire ne doit pas pas être vide.
         *
         * + Lister les commentaires en dessous, avec
         *      + nom utilisateur,
         *      + date de publication,
         *      + contenu du message
         */
        
        $em = $this->getDoctrine()->getManager();
        /*$repository = $em->getRepository(Comment::class);
        $comments = $repository->findBy(
            [
                'article' => $article
            ],
            [
                'publicationDate' => 'DESC'
            ]
        );*/
    
        $comment = new Comment();
        $comment->setPublicationDate(new \DateTime());
        $comment->setArticle($article);
        if (!is_null($this->getUser())) { // si l'utilisateur est connecté
            $comment->setUser($this->getUser());
        } else { // si c'est un anonyme
            $comment->setUser(null);
        }
        
        // création du formulaire relié à l'article
        $form = $this->createForm(CommentType::class, $comment);
    
        // le formulaire analyse la requête HTTP
        // et fait le mapping s'il a été soumis
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                
                $em->persist($comment);
                $em->flush();
                
                // message de confirmation
                $this->addFlash('success', 'Votre commentaire est enregistré');
                // redirection vers la page sur laquelle on est
                // pour ne pas être en POST
                return $this->redirectToRoute(
                    $request->get('_route'), // route de la page courante
                    [
                        'id' => $article->getId()
                    ]
                );
            } else {
                // message d'erreur
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }
        
        return $this->render(
            'article/index.html.twig',
            [
                'article' => $article,
//                'comments' => $comments,
                'form' => $form->createView()
            ]
        );
    }
}
