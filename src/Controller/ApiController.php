<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


use App\Entity\Produto;
use App\Entity\Categoria;
Use App\Entity\Imagem;

class ApiController extends Controller
{
    /**
     * @Route("/api/categoria", name="api_categorias")
     * @Method({"GET"})
     */
    public function getCategorias()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT c FROM \App\Entity\Categoria c WHERE c.ativo='1' ORDER BY c.nome";
        $query = $entityManager->createQuery($dql);
        $categorias = $query->getArrayResult();

        $response= new JsonResponse($categorias);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }


    /**
     * @Route("/api/categoria/{categoria_id}/produtos", name="api_categoria_produtos")
     * @Method({"GET"})
     */
    public function getCategoriaProdutos($categoria_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT p, i FROM \App\Entity\Produto p
        JOIN p.imagens i 
         WHERE p.visivel='1' and i.principal='1'
         AND p.categoria=$categoria_id ORDER BY p.nome";
        $query = $entityManager->createQuery($dql);
        $produtos = $query->getArrayResult();

        $response = new JsonResponse($produtos);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
        
    }    

    /**
     * @Route("/api/produto", name="api_produtos")
     * @Method({"GET"})
     */
    public function getProdutos()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT p, i, c FROM \App\Entity\Produto p 
        JOIN p.imagens i
        JOIN p.categoria c
         WHERE p.visivel='1' and i.principal='1'
          ORDER BY p.nome";
        $query = $entityManager->createQuery($dql);
        $produtos = $query->getArrayResult();

        $response= new JsonResponse($produtos);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
    /**
     * @Route("/api/produto/{produto_id}", name="api_produto")
     * @Method({"GET"})
     */
    public function getProduto($produto_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT p, i FROM \App\Entity\Produto p JOIN p.imagens i WHERE p.visivel='1' and p.id=$produto_id";
        $query = $entityManager->createQuery($dql);
        $produtos = $query->getArrayResult();

        $response= new JsonResponse($produtos[0]);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;

    }
    /**
     * @Route("/api/imagem/{produto_id}", name="api_imagens")
     * @Method({"GET"})
     */
    public function getImagens($produto_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT i FROM \App\Entity\Imagem i WHERE i.produto_id=$produto_id";
        $query = $entityManager->createQuery($dql);
        $imagens = $query->getArrayResult();

        $response=  new JsonResponse([
            $imagens
        ]);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
