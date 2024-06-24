<?php
namespace App\Controller;

use App\Repository\RoleRepository;
use App\Service\RoleService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/roles')]
class RoleController extends AbstractController
{
    private RoleService $roleService;
    private UserService $userService;

    public function __construct(RoleService $roleService, UserService $userService)
    {
        $this->roleService = $roleService;
        $this->userService = $userService;
    }

    


    #[Route('/extractGroups', name: 'app_groups')]
    public function getGroups(): Response
    {
        

        $rollys = $this->roleService->getAll();
        return $this->json($this->roleService->extractGroups($rollys));
    }

    #[Route('/bygroup', name:'')]
    public function getAllRolesSortedByGroup(): Response
    {
       

        return $this->json($this->roleService->getAllRolesSortedByGroup());
    }
}
