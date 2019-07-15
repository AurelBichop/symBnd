<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController {


/**
 * Montre la page et dit hello au prenom entré dans l'url
 *
 * @Route("/hello/{prenom}/age/{age}",name="hello", requirements={"age"="\d+"})
 * @Route("/hello",name="hello_base")
 * @Route("/hello/{prenom}",name="hello_prenom")
 */
    public function hello($prenom ="anonyme", $age=0){
        return $this->render(
            'hello.html.twig',
            [
                'prenom'=>$prenom,
                'age'=>$age
            ]

        );
    }

    /**
     * @Route("/",name="homepage")
     *
     */
    public function home(){

        $prenoms = ['lior'=>31,'jean'=>55, 'aure'=>12];


        return $this->render(
            'home.html.twig',
            [
                'title'=>'Bonjour title',
                'age'=>12,
                'tableau'=>$prenoms
            ]
        );
    }



}