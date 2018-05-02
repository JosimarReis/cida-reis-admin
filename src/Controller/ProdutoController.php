<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Produto;
use App\Entity\Categoria;
Use App\Entity\Imagem;

class ProdutoController extends Controller
{
        /**
     * @Route("/admin/produto", name="produto_todos")
     */
    public function todos()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT p FROM \App\Entity\Produto p ORDER BY p.nome";
        $query = $entityManager->createQuery($dql);
        $produtos = $query->getResult();
        
        return $this->render('admin/produto/lista.twig',[
            'produtos' => $produtos,
        ]);
    }
    /**
     * @Route("/admin/produto/novo", name="produto_novo")
     */
    public function novo(Request $request)
    {
        $produto = new Produto();

        $form = $this->createFormBuilder($produto)
        ->add('nome',TextType::class)
        ->add('categoria', EntityType::class, [
            'class' => Categoria::class,
            'choice_label' => function($categoria){
                return $categoria->getNome();
            }
        ])
        ->add('preco', MoneyType::class, [
            'divisor'=>100,
            'label'=>'Preço'
        ])
        ->add('visivel', ChoiceType::class, [
            'choices' => [
                'Sim'=>true,
                'Não' => false
            ]
        ])
        ->add('descricao',TextareaType::class, ['label'=>'Descrição'])
        ->add('salvar', SubmitType::class, ['label' =>'Salvar produto'])
        ->getForm();

        //recebe o formulario preechido
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $produto = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produto);
            $entityManager->flush();

            return $this->redirectToRoute('produto_imagens',[
                'produto_id'=>$produto->getId()
            ]);
        }
        



        return $this->render('admin/produto/formulario.twig',[
            'form' => $form->createView(),
            'title'=> 'Novo'
        ]);
    }



        /**
     * @Route("/admin/produto/alterar/{id}", name="produto_alterar")
     */
    public function alterar($id, Request $request){
        $entityManager = $this->getDoctrine()->getManager();

        $produto = $entityManager->getRepository(Produto::class)->find($id);

        if(!$produto){
            throw $this->createNotFoundException(
                'Não exite produto com o id => '.$id
            );
        }

        $form = $this->createFormBuilder($produto)
        ->add('nome',TextType::class)
        ->add('categoria', EntityType::class, [
            'class' => Categoria::class,
            'choice_label' => function($categoria){
                return $categoria->getNome();
            }
        ])
        ->add('preco', MoneyType::class, [
            'divisor'=>100,
            'label'=>'Preço'
        ])
        ->add('visivel', ChoiceType::class, [
            'choices' => [
                'Sim'=>true,
                'Não' => false
            ]
        ])
        ->add('descricao',TextareaType::class, ['label'=>'Descrição'])
        ->add('salvar', SubmitType::class, ['label' =>'Salvar produto'])
        ->getForm();

        //recebe o formulario preechido
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $produto = $form->getData();

            $entityManager->flush();

            return $this->redirectToRoute('produto_todos');
        }
        



        return $this->render('admin/produto/formulario.twig',[
            'form' => $form->createView(),
            'title'=> 'Alterar'
        ]);
    }

     /**
     * @Route("/admin/produto/remover/{id}", name="produto_remover")
     */
    public function remover($id){
        $entityManager = $this->getDoctrine()->getManager();

        $produto = $entityManager->getRepository(Produto::class)->find($id);
        $entityManager->remove($produto);
        $entityManager->flush();
        return $this->redirectToRoute('produto_todos');
    
        }

    /**
     * @Route("/admin/produto/upload/{produto}", name="produto_upload")
     */
    public function uploadAction($produto, Request $request) {


                $em = $this->getDoctrine()->getManager();
                $produto = $this->getDoctrine()
                ->getRepository(Produto::class)
                ->find($produto);
                //var_dump($request->files->all());
                $status = "";
                foreach ($request->files as $file) {
        
                    $status = array('status' => "success", "salvo" => false);
        
                    if (!is_null($file)) {
        
                        $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                        $path = $this->container->getParameter('upload_dir') . '/imagens';
                        $file->move($path, $fileName); // move the file to a path
                        $this->reduzImagem($path."/",$fileName);
                        $this->makeThumbnails($path.'/',$fileName);


                        $imagem = new Imagem();
                        $imagem
                        ->setProduto($produto)
                        ->setCaminho($fileName)
                        ->setPrincipal(false);
        
        
                        $em->persist($imagem);
                        $em->flush();
                        $status = [
                            'uploadName' => $fileName,
                            'success'=> true
                        ];
                    }
                    unset($file);
                }


        return new JsonResponse($status);
    }
    /**
     * @Route("/admin/produto/{produto_id}/imagens", name="produto_imagens")
     */
    public function imagens($produto_id)
    {
        $produto = $this->getDoctrine()
        ->getRepository(Produto::class)
        ->find($produto_id);

        return $this->render('admin/produto/imagens.twig',[
            'produto' => $produto
        ]);
    }
    /**
     * @Route("/admin/produto/{produto_id}/imagem/principal/{imagem_id}", name="produto_image_principal")
     */
    public function imagensPrincipal($produto_id, $imagem_id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->createQuery(
            'UPDATE App\Entity\Imagem i
            SET i.principal=0
            WHERE i.produto = :produto'
        )->setParameter('produto', $produto_id);
    
        $query->execute();
    

        $imagem = $entityManager->getRepository(Imagem::class)->find($imagem_id);
        $imagem->setPrincipal(true);
        $entityManager->flush();

        return $this->redirectToRoute('produto_imagens', ['produto_id'=>$produto_id]);

    }
    /**
     * @Route("/admin/produto/imagem/remover/{produto_id}/{imagem_id}", name="produto_imagem_remover")
     */
    public function imagemRemover($produto_id,$imagem_id)
    {
        $path = $this->container->getParameter('upload_dir') . '/imagens/';
        
        $imagem = $this->getDoctrine()
        ->getRepository(Imagem::class)
        ->find($imagem_id);
        
        $fileSystem = new Filesystem();
        $arquivos = [
            $path.$imagem->getCaminho(),
            $path.'thumb_'.$imagem->getCaminho(),
        ];
        if($fileSystem->exists($arquivos)){
            $fileSystem->remove($arquivos);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($imagem);
        $em->flush();            

        return $this->redirectToRoute('produto_imagens', ['produto_id'=>$produto_id]);

    }
        function makeThumbnails($updir, $img)
    {
        $thumbnail_width = 250;
        $thumbnail_height = 200;
        $thumb_beforeword = "thumb";
        $arr_image_details = getimagesize("$updir" .  "$img"); // pass id to thumb name
        $original_width = $arr_image_details[0];
        $original_height = $arr_image_details[1];
        if ($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }
        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);
        if ($arr_image_details[2] == IMAGETYPE_GIF) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        }
        if ($arr_image_details[2] == IMAGETYPE_JPEG) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        }
        if ($arr_image_details[2] == IMAGETYPE_PNG) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        }
        if ($imgt) {
            $old_image = $imgcreatefrom("$updir" .  "$img");
            $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
            imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            $imgt($new_image, "$updir" . "$thumb_beforeword"."_" . "$img");
        }
    }
    function reduzImagem($updir, $img)
    {
        $info = getimagesize($updir.$img);
        list($x, $y) = $info;
        $thumbnail_width = $x;
        $thumbnail_height = $y;
        $arr_image_details = getimagesize("$updir" .  "$img"); // pass id to thumb name
        $original_width = $arr_image_details[0];
        $original_height = $arr_image_details[1];
        if ($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }
        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);
        if ($arr_image_details[2] == IMAGETYPE_GIF) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        }
        if ($arr_image_details[2] == IMAGETYPE_JPEG) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        }
        if ($arr_image_details[2] == IMAGETYPE_PNG) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        }
        if ($imgt) {
            $old_image = $imgcreatefrom("$updir" .  "$img");
            $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
            imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            $imgt($new_image, "$updir" . "$img");
        }
    }
}
