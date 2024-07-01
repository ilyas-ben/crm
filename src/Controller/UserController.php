<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/users')]
class UserController extends AbstractController
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }



    #[Route('/add', name: 'app_users_add', methods: ['GET'])]
    public function addUser(Request $request): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "add", "users"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);

        return $this->render('users/addForm.html.twig');
    }



    #[Route('/index', name: 'users_list', methods: ['GET'])]
    public function index(): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "users"))
            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);

        return $this->render('users/usersList.html.twig');
    }




    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "users"))

            return new Response(Response::HTTP_FORBIDDEN);
        return $this->json($this->userService->getAll());
    }


    #[Route('/byid/{id}', methods: ['GET'])]
    public function getById(int $id): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "edit", "users") && $id !== $this->userService->getCurrentUserId())
            return new Response(Response::HTTP_FORBIDDEN);

        return $this->json($this->userService->getById($id));
    }


    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "add", "users"))

            return new Response(Response::HTTP_FORBIDDEN);

        $userJson = $request->getContent();
        return $this->json($this->userService->save(null, $userJson), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(Request $request, $id): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "edit", "users"))
            return new Response(Response::HTTP_FORBIDDEN);

        $userJson = $request->getContent();
        return $this->json($this->userService->edit($id, null, $userJson), Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteById($id): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "delete", "users"))
            return new Response("",Response::HTTP_FORBIDDEN);

        if ($this->userService->getCurrentUserId() == $id)

            return new Response("Impossible de supprimer votre compte alors que vous êtes connecté avec.", 403);

        $this->userService->deleteById($id);
        return new Response(200);
    }

}
