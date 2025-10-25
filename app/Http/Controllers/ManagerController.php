<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ManagerService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    protected $managerService;
    protected $userService;

    public function __construct(ManagerService $managerService, UserService $userService)
    {
        $this->managerService = $managerService;
        $this->userService = $userService;
    }

    //index
    public function index(){
        $user=Auth::id();
        $locataires= $this->managerService->allLocatairesByManager($user);
        $pending= $this->managerService->pendingLocataires();
        $stats = $this->managerService->getStats($user);

        return view("users.manager.index",compact("locataires","pending","stats"));
    }

    //create
    public function create(){
        $user=Auth::id();

        return view("users.manager.create");
    }

    //store
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'surname' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:locataire,admin,gestionnaire',
        ]);

        $user=Auth::id();

        $created= $this->managerService->createLocataire($validated,$user);
        if (!$created) {
            return redirect()->back()->with('error', 'Failed to create tenant.');
        }

        return redirect()->route('manager.index')->with('success', 'Tenant created successfully.');

    }

    //edit
    public function edit($id){
        $locataire = $this->userService->research(null)->where('id', $id)->first();
        if (!$locataire) {
            return redirect()->back()->with('error', 'Tenant not found.');
        }
        
        return view("users.manager.edit",compact('locataire'));
    }

    //update
    public function update(Request $request, int $id){
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'surname' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:locataire,admin,gestionnaire',
        ]);

        $user=Auth::id();

        $updated= $this->managerService->updateLocataire($id,$validated,$user);

        if (!$updated) {
            return redirect()->back()->with('error', 'Tenant not found.');
        }

        return redirect()->route('manager.index')->with('success', 'Tenant updated successfully.');

    }

    //destroy
    public function destroy(int $id){
        $user=Auth::id();
        $deleted= $this->managerService->deletelocataire($id, $user);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Tenant not found.');
        }

        return redirect()->route('manager.index')->with('success', 'Tenant deleted successfully.');
    }

    //search
    public function search(Request $request){
        $user=Auth::id();
        $term = $request->query('q'); 
        $locataires = $this->managerService->searchLocataires($term, $user);
        $pending= $this->managerService->pendingLocataires();
        $stats = $this->managerService->getStats($user);
        
        return view('users.manager.index', compact('locataires', 'pending','stats'));
    }

    //activate
    public function activate(int $id){
        $user=Auth::id();
        $activated= $this->managerService->activatelocataire($id, $user);

        if (!$activated) {
            return redirect()->back()->with('error', 'Tenant not found.');
        } 

        return redirect()->route('manager.index')->with('success', 'Tenant activated successfully.');
    }
 
    //deactivate
    public function deactivate(int $id){
        $user=Auth::id();
        $deactivated= $this->managerService->deactivatelocataire($id, $user);

        if (!$deactivated) {
            return redirect()->back()->with('error', 'Tenant not found.');
        }

        return redirect()->route('manager.index')->with('success', 'Tenant deactivated successfully.');
    }
}
