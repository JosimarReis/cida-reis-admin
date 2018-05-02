<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Categoria;

class CategoriaController extends Controller
{
    /**
     * @Route("/admin/categoria", name="categoria_todos")
     */
    public function todos()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT c FROM \App\Entity\Categoria c ORDER BY c.nome";
        $query = $entityManager->createQuery($dql);
        $categorias = $query->getResult();
        
        return $this->render('admin/categoria/lista.twig',[
            'categorias' => $categorias,
        ]);
    }

/**
     * @Route("/admin/categoria/novo", name="categoria_novo")
     */
    public function novo(Request $request)
    {
        $categoria = new Categoria();

        $form = $this->createFormBuilder($categoria)
        ->add('nome',TextType::class)
        ->add('ativo', ChoiceType::class, [
            'choices' => [
                'Sim'=>true,
                'Não' => false
            ]
        ])
        ->add('salvar', SubmitType::class, ['label' =>'Salvar categoria'])
        ->getForm();

        //recebe o formulario preechido
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $categoria = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categoria);
            $entityManager->flush();

            return $this->redirectToRoute('categoria_todos');
        }
        
        return $this->render('admin/categoria/formulario.twig',[
            'form' => $form->createView(),
            'title' => 'Nova'
        ]);
    }

    /**
     * @Route("/admin/categoria/{id}", name="categoria_mostrar")
     */
    public function mostrar($id){
        $categoria = $this->getDoctrine()
        ->getRepository(Categoria::class)
        ->find($id);

        if(!$categoria){
            throw $this->createNotFoundException(
                'Não exite categoria com o id => '.$id
            );
        }

        return new Response('O categoria buscada foi '. $categoria->getNome());
    }

        /**
     * @Route("/admin/categoria/alterar/{id}", name="categoria_alterar")
     */
    public function alterar($id, Request $request){
        $entityManager = $this->getDoctrine()->getManager();

        $categoria = $entityManager->getRepository(Categoria::class)->find($id);

        if(!$categoria){
            throw $this->createNotFoundException(
                'Não exite categoria com o id => '.$id
            );
        }

        $form = $this->createFormBuilder($categoria)
        ->add('nome',TextType::class)
        ->add('ativo', ChoiceType::class, [
            'choices' => [
                'Sim'=>true,
                'Não' => false
            ]
        ])
        ->add('salvar', SubmitType::class, ['label' =>'Salvar categoria'])
        ->getForm();

        //recebe o formulario preechido
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $categoria = $form->getData();

            $entityManager->flush();

            return $this->redirectToRoute('categoria_todos');
        }
        



        return $this->render('admin/categoria/formulario.twig',[
            'form' => $form->createView(),
            'title' => 'Alterar'
            
        ]);
    }

     /**
     * @Route("/admin/categoria/remover/{id}", name="categoria_remover")
     */
    public function remover($id){
        $entityManager = $this->getDoctrine()->getManager();

        $categoria = $entityManager->getRepository(Categoria::class)->find($id);
        $entityManager->remove($categoria);
        $entityManager->flush();
        return $this->redirectToRoute('categoria_todos');
    
        }
}
