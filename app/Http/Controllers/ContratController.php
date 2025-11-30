<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContratService;

class ContratController extends Controller
{
    protected $contratService;

    public function __construct(ContratService $contratService)
    {
      $this->contratService = $contratService;  
    }

    //index
    public function index(){
        $contrats=$this->contratService->all();
        return view('contrats.index', compact('contrats'));
    }

    //create
    public function create(){
        $locataires = $this->contratService->getAvailableTenants();

        $appartements = $this->contratService->getAvailableAppartements();

        return view('contrats.create', compact('locataires', 'appartements'));
    }

    //store
    public function store(Request $request){
        $data= $request->validate([
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after:start_date',
            'rent_amount'       => 'required|numeric|min:1',
            'rent_payment_day'  => 'required|integer|min:1|max:31',
            'deposit_amount'    => 'required|numeric|min:0',
            'tenant_id'         => 'required|exists:users,id',
            'appartement_id'    => 'required|exists:appartements,id',
        ]);

        $contrat=$this->contratService->create($data);

        if(!$contrat){
             return redirect()->back()->withInput()->with('error', 'Erreur lors de la creation du contrat.');
        }
        return redirect()->route('contrats.index')->with('success', 'Contrat créé avec succès.');
    }

    //edit
    public function edit($id){
        $contrat = $this->contratService->findById($id);
        // locataires disponibles + locataire actuel
        $locatairesDispos = $this->contratService->getAvailableTenants();
        $locataires = $locatairesDispos->push($contrat->tenant)->unique('id');

        // appartements disponibles + appartement actuel
        $appartsDispos = $this->contratService->getAvailableAppartements();
        $appartements = $appartsDispos->push($contrat->appartement)->unique('id');

        return view('contrats.edit', compact('contrat', 'locataires', 'appartements'));
    }

    //update
    public function update(Request $request, $id){
        $data=$request->validate([
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after:start_date',
            'rent_amount'       => 'required|numeric|min:1',
            'rent_payment_day'  => 'required|integer|min:1|max:31',
            'deposit_amount'    => 'required|numeric|min:0',
            'tenant_id'         => 'required|exists:users,id',
            'appartement_id'    => 'required|exists:appartements,id',
            'status'            => 'required'
        ]);

        $contrat=$this->contratService->update($id, $data);

        if(!$contrat){
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mis à jour du contrat.');
        }

         return redirect()->route('contrats.index')->with('success', 'Contrat mis à jour avec succès.');
    }

    //destroy
    public function destroy($id){
        $deleted= $this->contratService->delete($id);

        if(!$deleted){
            return redirect()->route('contrats.index')->with('error', 'Contrat non trouvé.');
        }
        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé.');
    }

    //search
    public function search(Request $request){
        $term = $request->query('q');
        $contrats = $this->contratService->search($term);
        return view('contrats.index', compact('contrats'));
    }

    //consult
    public function consult($id){
        $contrat=$this->contratService->findById($id);
        return view('contrats.consult',compact('contrat'));
    }

    //terminate
    public function terminate($id){
        $terminated=$this->contratService->terminate($id);

        if($terminated){
            return redirect()->route('contrats.index')->with('success', 'Contrat résilié.');
        }
        else{
            return redirect()->route('contrats.index')->with('error', 'Contrat non trouvé.');
        }
    }

    //renew
    public function renewForm($id)
    {
        $contrat = $this->contratService->findById($id);

        // On ne peut renouveler que si l'ancien contrat est expiré
        if (!$this->contratService->canBeRenewed($contrat->id)) {
            return back()->with('error', 'Ce contrat n’est pas encore expiré.');
        }

        return view('contrats.renew', [
            'contrat' => $contrat,
            'appartement' => $contrat->appartement,
            'tenant' => $contrat->tenant,
        ]);
    }

    public function renew(Request $request, $id)
    {
        $data=$request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:1',
            'rent_payment_day' => 'required|integer|min:1|max:31',
            'deposit_amount' => 'required|numeric|min:0'
        ]);

        $newContrat = $this->contratService->renew($id, $data);

        if($newContrat){
            return redirect()->route('contrats.index')->with('success', 'Contrat renouvelé avec succès !');   
        }
        else{
            return redirect()->back()->withInput()->with('error', 'Erreur lors du renouvelement du contrat.');
        }
        
    }

}