<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Services;
use App\Entity\Employer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormulaireController extends AbstractController
{
    
    /**
     * @Route("/",name="home")
     */
    public function home(){
            $employe = $this->getDoctrine()->getRepository(Employer::class)->findAll();
            return $this->render('formulaire/home.html.twig',
            array('employe'=> $employe));
            return $this->render('formulaire/home.html.twig');
    }
    /**
     * @Route("/formulaire/test",name="test")
     */
    public function test(Request $request, ObjectManager $manager){
    //    if (!$service) {
        $service= new Services();
     //  }
        
        $form= $this->createFormBuilder($service)
            ->add('libeller')
            ->getForm();
        $form->handleRequest($request);
        //dump($service);
        if($form->isSubmitted() && $form->isValid()){
           $manager->persist($service);
           $manager->flush();
        }
        return $this->render('formulaire/test.html.twig',[
            'formu'=>$form->createView()
        ]);
    }

    /**
     * @Route("/formulaire", name="formulaire")
     * @Route("/formulaire/{id}/edit", name="modif")
     */
    public function index(Employer $emploi=null, Request $request, ObjectManager $manager)
    {
        if (!$emploi) {
            $emploi= new Employer();
        }
        
        $f= $this->createFormBuilder($emploi)
            ->add('matricule', TextType::class,['attr'=>['class'=>'form-control']])
            ->add('service',EntityType::class ,[
                'class'=> Services::class,
                'choice_label'=>'libeller',
                'attr'=>['class'=>'form-control']
            ])
            ->add('fullname', TextType::class,['attr'=>['class'=>'form-control']])
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
                'attr'=>['class'=>'form-control']
            ])
            ->add('salaire', TextType::class,['attr'=>['class'=>'form-control']])
            ->getForm();
            $f->handleRequest($request);
            //dump($emploi);
            if($f->isSubmitted() && $f->isValid()){
                $manager->persist($emploi);
                $manager->flush();
             }
        return $this->render('formulaire/index.html.twig', [
            'controller_name' => 'FormulaireController',
            'f'=>$f->createView(),
            'modif'=>$emploi->getId()!==null
        ]);
    }

    /**
     * @Route("/formulaire/{id}/sup", name="supp")
    */
    public function suppresion(Employer $emploi,ObjectManager $manager){
        $manager->remove(($emploi));
        $manager->flush();
        return $this->redirectToRoute(('home'));
    
    }

}
