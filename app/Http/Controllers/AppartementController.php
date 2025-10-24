<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppartementService;

class AppartementController extends Controller
{
    protected $appartementService;
    
    public function __construct(AppartementService $appartementService)
    {
        $this->appartementService = $appartementService;
    }

    //index
    public function index(){
        $appartements = $this->appartementService->all();
       // On groupe les appartements par immeuble
        $immeubles = $appartements->groupBy(function ($appartement) {
            return $appartement->immeuble->name ?? 'Immeuble inconnu';
        });

        $stats = $this->appartementService->getStats();

        return view('appartements.index', compact('immeubles', 'stats'));
    }

    //create
    public function create(){ 
        $data=$this->appartementService->getFormData();
        return view('appartements.create',$data);
    }

    //store
    public function store(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'area' => 'required|numeric',
            'rent' => 'required|numeric',
            'status' => 'nullable|string|in:disponible,occupe,en_renovation',
            'immeuble_id' => 'required|integer|exists:immeubles,id',
            'locataire_id' => 'nullable|integer|exists:users,id',
        ]);
 
        $appartement = $this->appartementService->creates($data); 
        
        if (!$appartement) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création de l\'appartement.');
        }

        return redirect()->route('appartements.index')->with('success', 'Appartement créé avec succès.');
    }

    //edit
    public function edit($id)
    {
        $appartement = $this->appartementService->findById($id);
        $data=$this->appartementService->getFormData();

        $immeubles = $data['immeubles'];
        $locataires = $data['locataires'];
        return view('appartements.edit', compact('appartement', 'immeubles', 'locataires'));
    }


    //update
    public function update(Request $request, $id){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'area' => 'required|numeric',
            'rent' => 'required|numeric',
            'status' => 'nullable|string|in:disponible,occupe,en_renovation',
            'immeuble_id' => 'required|integer|exists:immeubles,id',
            'locataire_id' => 'nullable|integer|exists:users,id',
        ]);

        $appartement = $this->appartementService->update($id,$data);
        if (!$appartement) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise a jour de l\'appartement.');
        }

        return redirect()->route('appartements.index')->with('success', 'Appartement mis à jour avec succès.');
    }

    //destroy
    public function destroy($id){

        $deleted = $this->appartementService->delete($id);
        if ($deleted) {
            return redirect()->route('appartements.index')->with('success', 'Appartement supprimé avec succès.');
        } else {
            return redirect()->route('appartements.index')->with('error', 'Appartement non trouvé.');
        }
       
    }

    //consult
    public function consult($id){
        $appartement = $this->appartementService->findById($id);
        return view('appartements.consult',compact('appartement'));
    }

    //search
    public function search(Request $request)
    {
        $term = $request->query('q');

        // Récupérer les appartements filtrés
        $appartements = $this->appartementService->search($term);

        // Si search() renvoie un paginator, convertir en collection pour groupBy
        $appartementsCollection = collect($appartements->items());

        $immeubles = $appartementsCollection->groupBy(function ($appartement) {
            return $appartement->immeuble->name ?? 'Immeuble inconnu';
        });

        $stats = $this->appartementService->getStats();

        return view('appartements.index', compact('immeubles', 'stats'));
    }



}
