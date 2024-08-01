<?php

namespace App\Service;

use App\Entity\Profile;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use JsonException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserRepository $userRepository;
    private SerializerInterface $serializer;
    private UserPasswordHasherInterface $encoder;
    private ProfileService $profileService;
    private Security $security;
    private KernelInterface $kernel;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer, UserPasswordHasherInterface $encoder, ProfileService $profileService, Security $security , KernelInterface $kernel)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->encoder = $encoder;
        $this->profileService = $profileService;
        $this->security = $security;
        $this->kernel = $kernel;
    }

    public function getAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function getByid($id): ?User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        // Return user details as JSON response
        return $user;
    }


    public function save(User $user = null, string $userJson = null): User
    {
        if ($user != null)
            return $this->userRepository->save($user);

        $user = new User();
        $userData = json_decode($userJson, true);
        
        
        if (is_array($userData)) {
            // Set each attribute if present in $userData
            if (isset($userData['username'])) {
                $user->setUsername($userData['username']);
            }
            if (isset($userData['password'])) {
                $user->setPassword($userData['password']);
            }
            if (isset($userData['email'])) {
                $user->setEmail($userData['email']);
            }
            if (isset($userData['address'])) {
                $user->setAddress($userData['address']);
            }
            if (isset($userData['phone'])) {
                $user->setPhone($userData['phone']);
            }
            if (isset($userData['image'])) {
                $image64 = $userData['image'];
            }
        }

        //setting profile
        $profile = new Profile();
        $profile = $this->profileService->getById(json_decode($userJson, true)["profile"]["id"]);
        $user->setProfile($profile);

        // setting password
        $hashedPassword = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        // setting the image
        if($image64){
            $imageData = base64_decode($image64);
            throw new Exception($imageData);
            $newFilename = uniqid() . '.png';
            $imagePath = $this->kernel->getProjectDir() . '/public/images/' . $newFilename;
            try {
                file_put_contents($imagePath, $imageData);
            } catch (JsonException $e) {
                throw new JsonException('Failed to upload image');
            }

            $user->setImage('images/'. $newFilename);
        } 
        else throw new Exception(implode("," , $image64));

        return $this->userRepository->save($user);
    }

    public function edit($id, User $newUser = null, string $newUserJson = null): void
    {

        $oldUser = new User();

        if ($id) {
            $oldUser = $this->userRepository->find($id);
            if (!$oldUser) {
                throw new Exception('User not found');
            }
        }

        if ($newUser == null) {
            $newUser = $this->serializer->deserialize($newUserJson, User::class, 'json');

            //setting profile from userJson :
            $profile = new Profile();
            $profile = $this->profileService->getById(json_decode($newUserJson, true)["profile"]["id"]);
            $oldUser->setProfile($profile);
        }



        // setting user's infos
        $oldUser->setUsername($newUser->getUsername());
        $oldUser->setEmail($newUser->getEmail());
        $oldUser->setAddress($newUser->getAddress());
        $oldUser->setPhone($newUser->getPhone());

        //setting user's password
        if ($newUser->getPassword() === $oldUser->getPassword())
            $oldUser->setPassword($newUser->getPassword());
        else {
            $hashedPassword = $this->encoder->hashPassword($oldUser, $newUser->getPassword());
            $oldUser->setPassword($hashedPassword);
        }

        $this->userRepository->save($oldUser);
    }

    public function deleteById(int $id): void
    {
        $this->userRepository->deleteById($id);
    }

    public function userHasRoleByUserId(int $idUser, string $roleName, string $roleGroup): bool
    {   
        $user = $this->userRepository->find($idUser);

        foreach ($user->getProfile()->getRoles() as $role){
            if($role->getName() === $roleName && $role->getGroupe() === $roleGroup) return true;
        }

        return false;
    }

    public function getCurrentUserId() : int
    {
        return $this->security->getUser()->getId();
    }
    


}
?>