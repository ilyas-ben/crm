<?php

namespace App\Service;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Component\Serializer\SerializerInterface;

class RoleService
{
    private RoleRepository $roleRepository;
    private SerializerInterface $serializer;

    public function __construct(RoleRepository $roleRepository, SerializerInterface $serializer)
    {
        $this->roleRepository = $roleRepository;
        $this->serializer = $serializer;    
    }

    public function getAll(): array
    {
        return $this->roleRepository->findAll();
    }

    public function getById(int $id): Role
    {
        return $this->roleRepository->find($id);
    }

    public function extractGroups(array $roles): array
    {

        $b = array_unique(array_map(function ($role) {
            return $role->getGroupe();
        }, $roles));

        $finalGroups = [];

        foreach ($b as $a) {
            array_push($finalGroups, $a);
        }

        return $finalGroups;
    }


    public function extractAllExistingRolesGroups(): array
    {
        $roles = $this->getAll();

        $b = array_unique(array_map(function ($role) {
            return $role->getGroupe();
        }, $roles));

        $finalGroups = [];

        foreach ($b as $a) {
            array_push($finalGroups, $a);
        }

        return $finalGroups;
    }

    /* public function (): array
    {
        $roles = $this->getAll();
        $groups = $this->extractAllExistingRolesGroups();

        $result = [];

        array_push($result, $groups);


        foreach ($groups as $grp) {
            foreach ($roles as $r) {
                if ($r->getGroupe() == $grp) {
                    $result[$grp] = $r;
                }
            }
        }

        return $roles;
    } */

    public function getAllRolesSortedByGroup(): array
    {
        $roles = $this->getAll();
        $groupedRoles = [];

        // Regrouper les rôles par groupe
        foreach ($roles as $role) {
            $group = $role->getGroupe();

            // Vérifier si le groupe existe déjà dans le tableau, sinon initialiser un tableau vide
            if (!isset($groupedRoles[$group])) {
                $groupedRoles[$group] = [];
            }

            // Ajouter le rôle au groupe correspondant
            $groupedRoles[$group][] = $role;
        }

        return $groupedRoles;
    }
    public function getRolesSortedByGroup(array $roles): array
    {
        $groupedRoles = [];

        // Regrouper les rôles par groupe
        foreach ($roles as $role) {
            $group = $role->getGroupe();

            // Vérifier si le groupe existe déjà dans le tableau, sinon initialiser un tableau vide
            if (!isset($groupedRoles[$group])) {
                $groupedRoles[$group] = [];
            }

            // Ajouter le rôle au groupe correspondant
            $groupedRoles[$group][] = $role;
        }

        return $groupedRoles;
    }

}

?>