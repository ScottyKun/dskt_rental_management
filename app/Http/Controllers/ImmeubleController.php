<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ImmeubleService;

class ImmeubleController extends Controller
{
    protected $immeubleService;
    
    public function __construct(ImmeubleService $immeubleService)
    {
        $this->immeubleService = $immeubleService;
        $this->middleware('auth');
    }

    //index (selon user connecté)
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $immeubles = $this->immeubleService->all();
        } elseif ($user->role === 'gestionnaire') {
            $immeubles = $this->immeubleService->allByManager($user->id);
        } else {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        $stats = $this->immeubleService->getStats();

        return view('immeubles.index', compact('immeubles', 'stats'));
        
    }

    //store
    public function create()
    {
        //filtre selon le role pour remplir notre combobox
        $user = Auth::user();
        if ($user->role === 'admin') {
            $managers = $this->immeubleService->managers(); 
        } elseif ($user->role === 'gestionnaire') {
            $managers = collect([$user]);
        } else {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        return view('immeubles.create', compact('managers'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'required|integer|exists:users,id',
            'nb_apartments' => 'nullable|integer|min:0',
            'nb_available' => 'nullable|integer|min:0',
            'nb_occupied' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:actif,inactif',
        ]);

        $this->immeubleService->create($validated);
        return redirect()->route('immeubles.index')->with('success', 'Immeuble créé avec succès.');
    }

    //update
    public function edit(int $id)
    {
        $immeuble = $this->immeubleService->search(null)->where('id', $id)->first();

        if (!$immeuble) {
            return redirect()->back()->with('error', 'Immeuble introuvable.');
        }

        //filtre selon le role pour remplir notre combobox
        $user = Auth::user();
        if ($user->role === 'admin') {
            $managers = $this->immeubleService->managers();
        } elseif ($user->role === 'gestionnaire') {
            $managers = collect([$user]);
        } else {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        return view('immeubles.edit', compact('immeuble','managers'));
    }
    public function update(Request $request, int $id)
    {
         $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'town' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'nb_apartments' => 'nullable|integer|min:0',
            'nb_available' => 'nullable|integer|min:0',
            'nb_occupied' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:actif,inactif',
        ]);

        $updated = $this->immeubleService->update($id, $validated);

        if (!$updated) {
            return redirect()->back()->with('error', 'Mise à jour impossible.');
        }

        return redirect()->route('immeubles.index')->with('success', 'Immeuble mis à jour avec succès.');
    }

    //delete
    public function delete($id)
    {
        $deleted = $this->immeubleService->delete($id);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Immeuble introuvable.');
        }

        return redirect()->route('immeubles.index')->with('success', 'Immeuble supprimé avec succès.');
    }

    //search
    public function search(Request $request)
    {
        $term = $request->query('q'); // /immeubles/search?q=paris
        $immeubles = $this->immeubleService->search($term);
        $stats = $this->immeubleService->getStats();
        return view('immeubles.index', compact('immeubles', 'stats'));
    }

    //show
    public function show(int $id)
    {
        $immeuble = $this->immeubleService->findById($id);
        if (!$immeuble) {
            return redirect()->back()->with('error', 'Immeuble introuvable.');
        }
        return view('immeubles.consult', compact('immeuble'));      
    }


}
