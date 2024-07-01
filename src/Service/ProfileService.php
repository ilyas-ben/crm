<?php

namespace App\Service;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use App\Repository\RoleRepository;
use App\Service\RoleService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;


class ProfileService
{

    private ProfileRepository $profileRepository;
    private RoleService $roleService;
    private SerializerInterface $serializer;    

    public function __construct($profileRepository, $roleService, $serializer)
    {
        $this->profileRepository = $profileRepository;
        $this->roleService = $roleService;
        $this->serializer = $serializer;

    }

    public function save(Profile $profile = null, string $profileJson = null): Profile
    {
        if ($profile != null)
            return $this->profileRepository->save($profile);

        $profileData = json_decode($profileJson, true);

        // Create a new Profile entity
        $profile = new Profile();
        $profile->setName($profileData['name']);

        // Iterate through roles data and fetch or create Role entities
        foreach ($profileData['roles'] as $roleData) {
            // Fetch Role entity from database by ID
            $role = $this->roleService->getById($roleData['id']);

            if (!$role) {
                throw new Exception('Role not found for ID ' . $roleData['id']);
            }

            // Associate Role with Profile
            $profile->addRole($role);
        }


        return $this->profileRepository->save($profile);
    }

    public function getAll()
    {
        return $this->profileRepository->findAll();
    }

    public function getById($id): Profile
    {
        return $this->profileRepository->find($id);
    }

    public function getRolesByProfileId($profileId): array
    {
        $profile = $this->getById($profileId);
        return $this->roleService->getRolesSortedByGroup($profile->getRoles()->toArray());
    }


    public function edit($id/* , Profile $newProfile = null */, string $newProfileJson = null): Profile
    {

        $profileData = json_decode($newProfileJson, true);

        $oldProfile = new Profile();
        $oldProfile = $this->profileRepository->find($id);

        $oldProfile->setRoles(new ArrayCollection());

        if (!$oldProfile) {
            throw new ('Profile not found');
        }

    
        $oldProfile->setName($profileData['name']);
        
        $oldProfile->setRoles(new ArrayCollection());

        // Iterate through roles data and fetch or create Role entities
        
        foreach ($profileData['roles'] as $roleData) {
            // Fetch Role entity from database by ID
            $role = $this->roleService->getById($roleData['id']);

            if (!$role) {
                throw new Exception('Role not found for ID ' . $roleData['id']);
            }

            // Associate Role with Profile
            $oldProfile->addRole($role);
        }


         $this->profileRepository->save($oldProfile);
         return $this->getById($id);
    }

}
?>