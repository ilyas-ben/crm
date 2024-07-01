<?php

namespace App\Controller;

use App\Service\TiersService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tiers')]
class TiersController extends AbstractController
{


    private TiersService $tiersService;

    private UserService $userService;

    public function __construct(TiersService $tiersService, UserService $userService)
    {
        $this->tiersService = $tiersService;
        $this->userService = $userService;
    }


    #[Route('/index', name: 'tiers_list_page', methods: ['GET'])]
    public function index(): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "tiers"))
            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);

        return $this->render('tiers/tiersList.html.twig');
    }


    #[Route('', name: "app_tiers_getall", methods: ['GET'])]
    public function getAll(): Response
    {
       /*  if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "tiers"))

            return new Response('', Response::HTTP_FORBIDDEN); */
        return $this->json($this->tiersService->getAll());
    }

    #[Route('/byid/{id}', name: 'app_tiers_byid', methods: ['GET'])]
    public function getById(int $id): Response
    {
        /*  if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "tiers") && $id !== $this->userService->getCurrentUserId())
             return new Response('',Response::HTTP_FORBIDDEN); */

        return $this->json($this->tiersService->getById($id));
    }

    #[Route('/add', name: 'tiers_add_page', methods: ['GET'])]
    public function addTiers(Request $request): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "add", "tiers"))

            return new Response("<script>alert(\"Vous n'êtes pas autorisé pour cette action!\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);

        return $this->render('tiers/addForm.html.twig');
    }

    #[Route('', name: 'app_tiers_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        /* if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "add", "tiers"))
            return new Response('',Response::HTTP_FORBIDDEN); */

        $userJson = $request->getContent();
        return $this->json($this->tiersService->save(null, $userJson), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(Request $request, $id): Response
    {
        /* if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "edit", "tiers"))
            return new Response(Response::HTTP_FORBIDDEN); */

        $tiersJson = $request->getContent();
        return $this->json($this->tiersService->edit($id, null, $tiersJson), Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteById($id): Response
    {
        /* if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "delete", "tiers"))
            return new Response("", Response::HTTP_FORBIDDEN); */

        $this->tiersService->deleteById($id);
        return new Response("", 200);
    }

    // Dans TiersController
    #[Route('/additional-info/{tiersId}', name: 'tiers_additional_info', methods: ['GET'])]
    public function getAdditionalInfo(int $tiersId): Response
    {
        $additionalInfo = $this->tiersService->getAdditionalInfoByTiersId($tiersId);
        return $this->json($additionalInfo);
    }




}
