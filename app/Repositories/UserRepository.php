<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByName(string $name): ?User
    {
        return User::where('name', $name)->first();
    }

    public function all(){
        return User::all();
    }

    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function valider(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        $user->is_validated = true;
        return $user->save();
    }

    public function desactiver(int $id):bool{
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        $user->is_validated = false;
        return $user->save();
    }

    public function rechercher(?string $query)
    {
        
        $q = User::query();

        if ($query) {
            $q->where('name', 'like', "%{$query}%")
            ->orWhere('surname', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%");
        }

        return $q;
    }

    public function findByIdByAdminAndManagers() 
    {
        return User::whereIn('role', ['admin', 'gestionnaire'])->get();
    }

    public function findByManagers() 
    {
        return User::where('role', 'gestionnaire')->get();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    //rechercher les locataires sans appartements
    public function getLocatairesSansAppartement()
    {
        return User::where('role', 'locataire')
            ->where('is_validated','1')
            ->whereDoesntHave('appartement')
            ->orderBy('name', 'asc')
            ->get();
    }
}