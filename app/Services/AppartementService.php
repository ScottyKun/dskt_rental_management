<?php
namespace App\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Exception;



use App\Repositories\ImmeubleRepository;
use App\Repositories\AppartementRepository;
use App\Repositories\UserRepository;
use App\Repositories\ManagerRepository;


class AppartementService{

    protected $appartementRepository;
    protected $immeubleRepository;
    protected $userRepository;
    protected $managerRepository;
    public function __construct
    (AppartementRepository $appartementRepository, ImmeubleRepository $immeubleRepository, UserRepository $userRepository, ManagerRepository $managerRepository)
    {
        $this->appartementRepository = $appartementRepository;
        $this->immeubleRepository = $immeubleRepository;
        $this->userRepository = $userRepository;
        $this->managerRepository = $managerRepository;
    }

    //Creer un appartement
    public function creates(array $data)
    {
        DB::beginTransaction();
        
        try{
            $data['name'] = e($data['name']);
            $data['description'] = e($data['description'] ?? '');
            $data['type'] = e($data['type']);
            $data['area'] = e($data['area']);
             $data['rent'] = e($data['rent'] );
            $data['status'] = e($data['status'] ?? 'disponible');
            
            // Vérifie que l’immeuble existe
            $immeuble = $this->immeubleRepository->findById($data['immeuble_id']);
            if (!$immeuble) {
                throw ValidationException::withMessages([
                    'immeuble_id' => 'L\'immeuble spécifié n\'existe pas.'
                ]);
            }  

            // Vérifie que le locataire existe s’il est défini
        if (!empty($data['locataire_id'])) {
                    $locataire = $this->userRepository->findById($data['locataire_id']);
                    if (!$locataire) {
                        throw ValidationException::withMessages([
                            'locataire_id' => 'Le locataire spécifié n\'existe pas.'
                        ]);
                    }
            }

            //creatin de l'appartement
            $appartement = $this->appartementRepository->create($data);
            
            //update des champs de l’immeuble
            if ($appartement->status === 'occupe'&& !empty($data['locataire_id'])) {
                    $immeuble->nb_occupied += 1;
                    $immeuble->nb_available -= 1;
            }
            
            // Sauvegarde les changements
            $immeuble->save();
            DB::commit();
            return $appartement;

        }catch (Exception $e) {
            DB::rollBack();
            throw $e;
        } 

    }     

    //update un appartement
    public function update(int $id, array $data): bool
    {
        $appartement = $this->appartementRepository->findById($id);

        if (!$appartement) {
            throw ValidationException::withMessages([
                'id' => 'Appartement introuvable.'
            ]);
        }

        $data['name'] = e($data['name'] ?? $appartement->name);
        $data['description'] = e($data['description'] ?? $appartement->description);
        $data['type'] = e($data['type'] ?? $appartement->type);
        $data['area'] = e($data['area'] ?? $appartement->area);
        $data['rent'] = e($data['rent'] ?? $appartement->rent);
        $data['status'] = e($data['status'] ?? $appartement->status);

        
        return $this->appartementRepository->update($id, $data);
 
    }

    //supprimer un appartement
    public function delete(int $id): bool
    {
        DB::beginTransaction();

        try {
            $appartement = $this->appartementRepository->findById($id);
            if (!$appartement) {
                throw ValidationException::withMessages([
                    'id' => 'Appartement introuvable.'
                ]);
            }

            $immeuble = $appartement->immeuble;
            if ($immeuble) {
               
                if ($appartement->status === 'occupe') {
                    $immeuble->nb_occupied -= 1;
                    $immeuble->nb_available += 1;
                }

                $immeuble->save();
            }

            $deleted = $this->appartementRepository->delete($id);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //rechercher
    public function search(?string $term = null, $perPage = 7)
    {
        return $this->appartementRepository->search($term, $perPage);
    }

    //find un appartement by id
    public function findById(int $id)
    {
        return $this->appartementRepository->findById($id);
    }

    //all
    public function all(){
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->appartementRepository->all();
        }

        if ($user->role === 'gestionnaire') {
            $immeubles = $this->immeubleRepository->findByGestionnaire($user->id);
            $immeubleIds = $immeubles->pluck('id')->toArray();

            return $this->appartementRepository->findByImmeubleIds($immeubleIds);
        }
    }

    //getData
    public function getFormData()
    {
        $user = Auth::user();

        // Immeubles selon le rôle
        if ($user->role === 'admin') {
            $immeubles = $this->immeubleRepository->findAll();
        } else {
            $immeubles = $this->immeubleRepository->findByGestionnaire($user->id);
        }

        // Locataires (optionnel)
        if($user->role==='admin'){
            $locataires = $this->userRepository->getLocatairesSansAppartement();
        }else{
            $locataires= $this->managerRepository->getLocataires($user->id);
        }
        

        return compact('immeubles', 'locataires');
    }

    //getstats
    public function getStats()
    {
        return [
            'total'        => $this->appartementRepository->countAll(),
            'disponibles'  => $this->appartementRepository->countDisponibles(),
            'occupes'      => $this->appartementRepository->countOccupes(),
            'renovation'   => $this->appartementRepository->countEnRenovation(),
        ];
    }

   
}