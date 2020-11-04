<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Repository\ArticleRepository;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Article::class);
       
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }

      /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null  ,Request $request , ManagerRegistry $managerRegistry ){
          if (!$article){
            $article = new Article();
          }
           

            $form = $this->createFormBuilder($article)
                        ->add('title')
                        ->add('content')
                        ->add('image')
                        ->getForm();

            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                if (!$article->getId()){
                    $article->setCreatedAt(new \DateTime());
                }
                 
                 $em = $managerRegistry->getManager();

                 $em->persist($article);
                 $em->flush();

                 return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
            }
        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !==null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show( Article $article){
        $repo = $this->getDoctrine()->getRepository(Article::class);

       // $article = $repo->find($id);
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }

   
}
