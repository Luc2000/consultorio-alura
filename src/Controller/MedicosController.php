<?php

namespace App\Controller;

use App\Entity\Medico;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController 
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/medicos", methods={"POST"})
    */
    public function novo(Request $request) : Response
    {
        $corpoRequisicao = $request->getContent();
        $dadoEmJson = json_decode($corpoRequisicao);

        $medico = new Medico();
        $medico->crm = $dadoEmJson->crm;
        $medico->nome = $dadoEmJson->nome;

        //Com esse persist vc "observa" o medico, tudo que vem de lá
        $this->entityManager->persist($medico);
        //Dá para fazer mil coisas agora, ai depois que fizer tem que enviar:
        $this->entityManager->flush();



        return new JsonResponse($medico);
    }

    /**
     * @Route("/medicos", methods={"GET"})
    */
    public function buscarTodos(): Response
    {
        $repositorioDeMedicos = $this
            ->getDoctrine()
            ->getRepository(persistentObject:Medico::class);
        $medicoList = $repositorioDeMedicos->findAll();

        return new JsonResponse($medicoList);
    }

    /**
     * @Route("/medicos/{id}", methods={"GET"})
    */
    public function buscarUm(int $id): Response
    {
        $repositorioDeMedicos = $this
            ->getDoctrine()
            ->getRepository(persistentObject:Medico::class);
        $medico = $repositorioDeMedicos->find(id:$id);
        if($medico){
            $codigoRetorno = Response::HTTP_OK;
        }else{
            $codigoRetorno = Response::HTTP_NO_CONTENT;
        }
        return new JsonResponse($medico, $codigoRetorno);
    }

    /**
     * @Route("/medicos/{id}", methods={"PUT"})
    */
    public function atualiza(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $dadoEmJson = json_decode($corpoRequisicao);

        $medicoEnviado = new Medico();
        $medicoEnviado->crm = $dadoEmJson->crm;
        $medicoEnviado->nome = $dadoEmJson->nome;

        $repositorioDeMedicos = $this
            ->getDoctrine()
            ->getRepository(persistentObject:Medico::class);
        $medicoExistente = $repositorioDeMedicos->find($id);

        if(!$medicoExistente){
            return new Response(content:'', status:Response::HTTP_I_AM_A_TEAPOT);
        }

        $medicoExistente->crm = $medicoEnviado->crm;
        $medicoExistente->nome = $medicoEnviado->nome;

        //Dá para fazer mil coisas agora, ai depois que fizer tem que enviar:
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente);

    }
}