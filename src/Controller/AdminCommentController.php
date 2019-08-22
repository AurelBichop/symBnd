<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * Affiche la liste des commentaires
     * 
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $comments,$page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                    ->setLimit(5)
                    ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination'=>$pagination
        ]);
    }


    /**
     * Permet de modifier un commentaire
     * 
     *@Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     * 
     * @return Response
     */
    public function edit(Comment $comment,Request $request, ObjectManager $manager){

        $form = $this->createForm(AdminCommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "le commentaire n° {$comment->getId()} a été mis à jour"
            );
        }

        return $this->render('admin/comment/edit.html.twig',[
            'comment'=>$comment,
            'form'=>$form->createView()
        ]);
    }

    //delete et message flash

    /**
     * Permet de supprimer un commentaire
     *@Route("/admin/comments/{id}/delete",name="admin_comments_delete")
     * 
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager){

        $manager->remove($comment);
        $manager->flush();
    
            $this->addFlash(
                'success',
                "Le commentaire de <strong> {$comment->getAuthor()->getfirstName()} {$comment->getAuthor()->getlastName()} </strong> à bien été supprimé"
            );

        return $this->redirectToRoute('admin_comments_index');

    }
}
