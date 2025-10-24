<?php
namespace App\Repositories;
use App\Models\Role;

class RoleRepository
{
   
    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    public function all(){
        return Role::all();
    }
}