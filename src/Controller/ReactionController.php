<?php

namespace App\Controller;

use App\Entity\Reaction;
use App\Repository\PostRepository;
use App\Repository\ReactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReactionController extends AbstractController
{
    #[Route('/reaction/post/{id}', name: 'app_reaction')]
    public function index($id, EntityManagerInterface $entityManager, PostRepository $postRepository, ReactionRepository $reactionRepository): Response
    {

        if (!$this->getUser()){

            $this->addFlash('info', "you must be logged in to like this post");

            return  $this->redirectToRoute("app_home_page");
        }

        $postId = $postRepository->find($id);
        $user = $this->getUser();

        $verify = $reactionRepository->findOneBy(['post'=>$postId,"author"=>$user],[]);

        if ($verify === null){

            $reaction = new Reaction();
            $reaction->setAuthor($user);
            $reaction->setPost($postId);
            $entityManager->persist($reaction);
            $entityManager->flush();
        }else{

            $entityManager->remove($verify);
            $entityManager->flush();
        }


        return $this->redirectToRoute("app_home_page");
    }


    #[Route('/reaction/users/post/{id}', name: 'app_reaction_users')]
    public  function ReactionPostUser($id, PostRepository $postRepository, ReactionRepository $reactionRepository):Response
    {

        $postId = $postRepository->find($id);
        $reactionsUsers = $reactionRepository->findBy(['post'=>$postId]);

        return $this->render('reaction/index.html.twig',[
            "users"=>$reactionsUsers
        ]);

    }
}
