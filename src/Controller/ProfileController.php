<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\RoleRepository;
use App\Service\RoleService;
use App\Service\ProfileService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/profiles')]
class ProfileController extends AbstractController
{

    private ProfileService $profileService;
    private UserService $userService;



    /* public function __construct(ProfileRepository $profileRepository, RoleService $roleService, SerializerInterface $serializer)
    {
        $this->profileService = new ProfileService($profileRepository, $roleService, $serializer);
    } */

    public function __construct(ProfileService $profileService, UserService $userService)
    {
        $this->profileService = $profileService;
        $this->userService = $userService;
    }


    #[Route('/index', name: 'app_profiles')]
    public function index(Request $request): Response
    {
        /* if(!hasRole($users,"add")) */

        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "profiles"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);


        return $this->render('profiles/profiles.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }


    #[Route('', methods: ['GET'])]
    public function getAll(): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "profiles"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);
            
        return $this->json($this->profileService->getAll());
    }

    #[Route('/{id}/roles', methods: ['GET'])]
    public function getRolesByProfileId(int $id): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "roles"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);

        return $this->json($this->profileService->getRolesByProfileId($id));
    }

    #[Route('/add', name: 'app_profile_add_form', methods: ['GET'])]
    public function addForm(): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "add", "profiles"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);
        return $this->render('profiles/addForm.html.twig');
    }


    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {

        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "add", "profiles"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);

        $profileJson = $request->getContent();
        return $this->json($this->profileService->save(null, $profileJson));
    }


    #[Route('/byid/{id}', name: 'app_profile_get_By_Id', methods: ['GET'])]

    public function getById(int $id): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "show", "profiles"))

            return new Response("<script>alert(\"You dont have the rights ! , please go back\");location.href=\" / \"</script>", Response::HTTP_FORBIDDEN);
        return $this->json($this->profileService->getById($id));
    }

    #[Route('/{id}', name: 'app_profile_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): Response
    {
        if (!$this->userService->userHasRoleByUserId($this->userService->getCurrentUserId(), "showRoles", "profiles"))

            return new Response(Response::HTTP_FORBIDDEN);

        $profileJson = $request->getContent();
        return $this->json($this->profileService->edit($id, $profileJson));
    }



}
