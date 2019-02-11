<?php
    
    
    namespace App\Controller\Admin;
    
    
    use App\Entity\Article;
    use App\Form\ArticleType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\Routing\Annotation\Route;

    /**
     * Class ArticleController
     * @package App\Controller\Admin
     * @Route("/article")
     */
    class ArticleController extends AbstractController
    {
        /**
         * @Route("/")
         */
        public function index()
        {
            
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Article::class);
            $articles = $repository->findBy([], ['publicationDate' => 'DESC']);
            
            return $this->render(
                'admin/article/index.html.twig',
                [
                    'articles' => $articles
                ]
            );
        }
        
        /*
         * + Ajouter la méthode edit() qui fait
         *   le rendu du formulaire et son traitement
         * + Mettre un lien 'ajouter' dans la page de liste
         * + Validation : tous les champs obligatoires
         * + En création :
         *      + setter l'auteur avec l'utilisateur connecté
         *          ($this->getUser() depuis un contrôleur)
         *      + setter la date de publication à maintenant
         * + Adapter la route et le contenu de la méthode pour
         *   que la page fonctionne en modification
         * + Ajouter le bouton 'Modifier' dans la page de liste
         * + Enregistrement en bdd
         *      + redirection vers page de liste
         *      + avec message de confirmation
         */
    
        /**
         * {id} est optionnel et doit être un nombre
         * @Route("/edition/{id}", defaults={"id": null}, requirements={"id" : "\d+"})
         */
        public function edit(Request $request, $id)
        {
            $em = $this->getDoctrine()->getManager();
    
            if (is_null($id)) { // création
                $article = new Article();
                // set auteur et date de publication
                $article->setAuthor($this->getUser());
                $article->setPublicationDate(new \DateTime('now'));
            } else { // modification
                $article = $em->find(Article::class, $id);
        
                // 404 si l'id reçu dans l'url n'est pas en bdd
                if (is_null($article)) {
                    throw new NotFoundHttpException();
                }
            }
    
    
            // création du formulaire relié à l'article
            $form = $this->createForm(ArticleType::class, $article);
            
            // le formulaire analyse la requête HTTP
            // et fait le mapping s'il a été soumis
            $form->handleRequest($request);
    
            dump($article);
    
            // si le formulaire est envoyé
            if ($form->isSubmitted()) {
                // si les validations (des annotations dans l'entity Category)
                // sont passées
                if ($form->isValid()) {
                    
                    
                    // enregistrement de la catégorie en bdd
                    $em->persist($article);
                    $em->flush();
            
                    // message de confirmation
                    $this->addFlash('success', 'L\'article est enregistré');
                    // redirection vers la liste
                    return $this->redirectToRoute('app_admin_article_index');
                } else {
                    // message d'erreur
                    $this->addFlash('error', 'Le formulaire contient des erreurs');
                }
            }
    
            return $this->render(
                'admin/article/edit.html.twig',
                [
                    // passage du formulaire au template
                    'form' => $form->createView()
                ]
            );
        }
    
        /**
         * @Route("/delete/{id}")
         */
        public function delete(Article $article)
        {
            $em = $this->getDoctrine()->getManager();
        
            $em->remove($article);
            $em->flush();
        
            $this->addFlash('success', 'L\'article est supprimé');
        
            return $this->redirectToRoute('app_admin_article_index');
        }
    }