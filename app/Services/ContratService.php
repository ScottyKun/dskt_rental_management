<?php
namespace App\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Repositories\ContratRepository;
use App\Repositories\AppartementRepository;
use App\Repositories\ImmeubleRepository;
use App\Repositories\UserRepository;
class ContratService
{
    protected $contratRepository;
    protected $appartementRepository;
    protected $immeubleRepository;
    protected $userRepository;

    public function __construct(ContratRepository $contratRepository, AppartementRepository $appartementRepository, ImmeubleRepository $immeubleRepository, UserRepository $userRepository)
    {
        $this->contratRepository = $contratRepository;
        $this->appartementRepository = $appartementRepository;
        $this->immeubleRepository = $immeubleRepository;
        $this->userRepository = $userRepository;
    }

    //creer un contrat
    public function create(array $data)
    {
         DB::beginTransaction();
        // Validation des données
        try{
            $data['start_date'] = e($data['start_date']);
            $data['end_date'] = e($data['end_date']);
            $data['rent_amount'] = e($data['rent_amount']);
            $data['rent_payment_day'] = e($data['rent_payment_day']);
            $data['deposit_amount'] = e($data['deposit_amount']);
            $data['status'] = e($data['status'] ?? 'actif');
            $data['tenant_id'] = e($data['tenant_id']);
            $data['appartement_id'] = e($data['appartement_id']);

            $contrat=$this->contratRepository->create($data);

            //si rent de appartement different de rent_amount, update rent de appartement
            $appartement = $this->appartementRepository->findById($data['appartement_id']);
            if($appartement->rent != $data['rent_amount']){
                $this->appartementRepository->update($appartement->id, ['rent' => $data['rent_amount']]);
            }

            //si locataire pas encore assigne
            if(! $appartement->locataire_id){
                $this->appartementRepository->update($appartement->id, ['locataire_id' => $data['tenant_id'],'status' => 'occupe']);
                $immeuble= $this->immeubleRepository->findById($appartement->immeuble_id);
                $immeuble->nb_occupied+=1;
                $immeuble->nb_available-=1;
                $immeuble->save();
            }
            // Sauvegarde les changements
            $appartement->save();
            DB::commit();
            return $contrat;
        }
        catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
        
    }

    //supprimer un contrat
    public function delete(int $id): bool
    {
        $contrat = $this->contratRepository->findById($id);
        if (!$contrat) {
            throw ValidationException::withMessages([
                'id' => 'Contrat introuvable.'
            ]);
        }

        return $this->contratRepository->delete($id);  
    }

    //modifier un contrat
    public function update(int $id, array $data): bool
    {
        $contrat = $this->contratRepository->findById($id);

        if (!$contrat) {
            throw ValidationException::withMessages([
                'id' => 'Contrat introuvable.'
            ]);
        }

        DB::beginTransaction();
        try{
            $data['start_date'] = e($data['start_date'] ?? $contrat->start_date);
            $data['end_date'] = e($data['end_date'] ?? $contrat->end_date);
            $data['rent_amount'] = e($data['rent_amount'] ?? $contrat->rent_amount);
            $data['rent_payment_day'] = e($data['rent_payment_day'] ?? $contrat->rent_payment_day);
            $data['deposit_amount'] = e($data['deposit_amount'] ?? $contrat->deposit_amount);
            $data['status'] = e($data['status'] ?? $contrat->status);
            $data['tenant_id'] = e($data['tenant_id'] ?? $contrat->tenant_id);
            $data['appartement_id'] = e($data['appartement_id'] ?? $contrat->appartement_id);

            //
            $oldAppart = $this->appartementRepository->findById($contrat->appartement_id);
            $oldIm= $this->immeubleRepository->findById($oldAppart->immeuble_id);


            //si on change de appartement
            if($data['appartement_id'] != $contrat->appartement_id){
                $newAppart = $this->appartementRepository->findById($contrat->$data['appartement_id']);
                $newIm= $this->immeubleRepository->findById($newAppart->immeuble_id);

                //liberer ancien
                $oldAppart->locataire_id = null;
                $oldAppart->status = 'disponible';

                $oldIm->nb_occupied -= 1;
                $oldIm->nb_available += 1;

                //assigne le nouveau
                $newAppart->locataire_id = $data['tenant_id'];
                $newAppart->status       = 'occupe';

                $newIm->nb_occupied += 1;
                $newIm->nb_available -= 1;

                $oldAppart->save();
                $newAppart->save();
                $oldIm->save();
                $newIm->save();

            }
            //si on change juste de locataire
            elseif($contrat->tenant_id != $data['tenant_id']){
                $oldAppart->locataire_id = $data['tenant_id'];
                $oldAppart->save();
            }

            //si changement de loyer
            if($oldAppart->rent != $data['rent_amount']){
                $this->appartementRepository->update($oldAppart->id, ['rent' => $data['rent_amount']]);
            }

            DB::commit();
            return $this->contratRepository->update($id, $data);
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //search
     public function search(?string $term = null, $perPage = 7)
    {
        return $this->contratRepository->search($term, $perPage);
    }

    //all contrat
    public function all()
    {
        $user = Auth::user();

        if($user->role === 'admin'){
            return $this->contratRepository->all();
        }

        if($user->role === 'gestionnaire'){
            return $this->contratRepository->listByManager($user->id);
        }
        if($user->role === 'locataire'){
            return $this->contratRepository->listByTenant($user->id);
        }
    }

    /**
     * KPI de comptage (total, actifs, resilies, expires), scopes par role,
     * calcules directement en base (independant de la pagination de all()).
     */
    public function counts(): array
    {
        $user = Auth::user();

        return match ($user->role) {
            'gestionnaire' => $this->contratRepository->countsByManager($user->id),
            'locataire' => $this->contratRepository->countsByTenant($user->id),
            default => $this->contratRepository->countsAll(),
        };
    }

    //appartements sans contrat actifs
    public function getAvailableAppartements()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->appartementRepository->appartementsSansContratActif();
        }

        if ($user->role === 'gestionnaire') {
            return $this->appartementRepository->appartementsSansContratActifByManager($user->id);
        }
    }

    //locataire sans contrat actifs
    public function getAvailableTenants()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->userRepository->locatairesSansContrat();
        }

        if ($user->role === 'gestionnaire') {
            return $this->userRepository->locatairesSansContratByManager($user->id);
        }
    }

    //resilier un contrat
    public function terminate(int $id)
    {
        //status contrat = resilie; status appart= disponnible; nb_available++
        $contrat = $this->contratRepository->findById($id);

        if (!$contrat) {
            throw ValidationException::withMessages([
                'id' => 'Contrat introuvable.'
            ]);
        }

        DB::beginTransaction();

        try{
            $contrat->status='résilié';

            $appartement=$this->appartementRepository->findById($contrat->appartement_id);
            $appartement->status='disponible';

            $immeuble= $this->immeubleRepository->findById($appartement->immeuble_id);
            $immeuble->nb_occupied-=1;
            $immeuble->nb_available+=1;

            $contrat->save();
            $appartement->save();
            $immeuble->save();
            DB::commit();

            return true;

        }
        catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function findById($id){
        return $this->contratRepository->findById($id);
    }

    //renouveler le contrat
    public function canBeRenewed(int $id)
    {
        $contrat=$this->contratRepository->findById($id);
        return now()->gt($contrat->end_date);
    }

    public function renew(int $oldContratId, array $data)
    {
        $old = $this->contratRepository->findById($oldContratId);

        if (!$this->canBeRenewed($old->id)) {
            throw  ValidationException::withMessages([
                'id' => 'Contrat non expiré.'
            ]);
        }

        // Nouveau contrat
        $newContrat = [
            'tenant_id' => $old->tenant_id,
            'appartement_id' => $old->appartement_id,
            'rent_amount' => $data['rent_amount'],
            'rent_payment_day' => $data['rent_payment_day'],
            'deposit_amount' => $data['deposit_amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'actif'
        ];

        return $this->contratRepository->create($newContrat);
    }
}