<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\WebhookRepository;
use App\Services\MessageService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $paymentRepository;
    protected $receiptRepository;
    protected $webhookRepository;
    protected $messageService;

    public function __construct(
        PaymentRepository $paymentRepository,
        ReceiptRepository $receiptRepository,
        WebhookRepository $webhookRepository,
        MessageService $messageService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->receiptRepository = $receiptRepository;
        $this->webhookRepository = $webhookRepository;
        $this->messageService = $messageService;
    }

    //rdv paiement
    public function requestCashPaymentAppointment(int $tenantId)
    {
        $title = 'Demande de rendez-vous pour paiement en espèces';
        $content = 'Un locataire souhaite effectuer un paiement en espèces.';

        $this->messageService->sendTenantRequest($tenantId, [
            'title' => $title,
            'content' => $content
        ]);

        return true;
    }

    //Paiement en especes
    public function createCashPayment(array $data)
    {
        if (auth()->user()->role === 'locataire') {
        throw ValidationException::withMessages([
            'authorization' => 'Action non autorisée.'
        ]);}
        
        try {
            DB::beginTransaction();

            // Récupérer la méthode de paiement "CASH"
            $cashPaymentMethod = $this->paymentRepository->findByCode('CASH');
            
            if (!$cashPaymentMethod) {
                // Vérifier si l'utilisateur veut créer la méthode de paiement
                if (!isset($data['create_payment_method']) || $data['create_payment_method'] !== 'yes') {
                    throw ValidationException::withMessages([
                        'payment_method' => 'Pas de méthode de paiement. Voulez-vous la créer ?',
                    ])->errorBag('confirmation');
                }
                
                if ($data['create_payment_method'] == 'yes'){
                    // Créer la méthode de paiement CASH
                    $cashPaymentMethod = $this->paymentRepository->create([
                        'code' => 'CASH',
                        'label' => 'Espèces',
                        'is_active' => true
                    ]);
                }
                
            }

            // Créer le paiement
            $payment = $this->paymentRepository->createPayment([
                'tenant_id' => intval($data['tenant_id']),
                'manager_id' => intval($data['manager_id']),
                'payment_method_id' => $cashPaymentMethod->id,
                'amount' => floatval($data['amount']),
                'currency' => e($data['currency'] ?? 'CFA'),
                'status' => 'CONFIRMED',
                'paid_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Paiement enregistré avec succès'
            ];

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage()
            ]);
        }
    }
    //Generation de recu
    public function generateReceipt(int $paymentId, array $periods, int $generatedBy)
    {
        if (auth()->user()->role === 'locataire') {
        throw ValidationException::withMessages([
            'authorization' => 'Action non autorisée.'
        ]);}

        try {
            DB::beginTransaction();

            // Récupérer le paiement
            $payment = $this->paymentRepository->findPaymentById($paymentId);
            
            if (!$payment) {
                throw new \Exception('Paiement non trouvé');
            }

            if (!$payment->isConfirmed()) {
                throw new \Exception('Le paiement doit être confirmé pour générer un reçu');
            }

            // Générer un numéro de reçu unique
            $receiptNumber = $this->generateUniqueReceiptNumber();

            // Créer le reçu
            $receipt = $this->receiptRepository->create([
                'receipt_number' => $receiptNumber,
                'payment_id' => $payment->id,
                'tenant_id' => $payment->tenant_id,
                'total_amount' => $payment->amount,
                'generated_by' => $generatedBy,
                'generated_at' => now()
            ]);

            // Créer les périodes de loyer associées
            foreach ($periods as $period) {
                $this->receiptRepository->createReceiptPeriod([
                    'receipt_id' => $receipt->id,
                    'period_start' => $period['period_start'],
                    'period_end' => $period['period_end']
                ]);
            }

            // Envoyer une notification au locataire
            $this->messageService->create([
                'sender_id' => $generatedBy,
                'receiver_id' => $payment->tenant_id,
                'title' => 'Reçu de paiement généré',
                'content' => "Votre reçu de paiement n°{$receiptNumber} d'un montant de {$payment->amount} {$payment->currency} a été généré. Vous pouvez le consulter dans votre espace.",
                'is_read' => false
            ]);

            DB::commit();

            return $receipt;

        } catch (ValidationException $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' => ['Erreur lors de la génération du reçu: ' . $e->getMessage()]
            ]);
        }
    }
    private function generateUniqueReceiptNumber(): string
    {
        $prefix = 'REC';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        return "{$prefix}-{$date}-{$random}";
    }
    //all paiement (selon user)
    public function getAllPayments()
    {
        $role = auth()->user()->role;
        $userId = auth()->user()->id;

        // Si admin : tous les paiements
        if ($role === 'admin') {
            return $this->paymentRepository->getAllPayments();
        }
        
        // Si gestionnaire : paiements de ses locataires
        if ($role === 'gestionnaire') {
            return $this->paymentRepository->getPaymentsByManager($userId);
        }
        
        // Si locataire : ses propres paiements
        if ($role === 'locataire') {
            return $this->paymentRepository->getPaymentsByTenant($userId);
        }

    }

    //update paiement
    public function updatePayment(int $id, array $data)
    {
        try {
            $payment = $this->paymentRepository->findPaymentById($id);

            if (!$payment) {
                throw ValidationException::withMessages([
                    'payment' => ['Paiement introuvable.']
                ]);
            }

            $data['amount'] = e($data['amount']) ?? $payment->amount;
            $data['tenant_id'] = e($data['tenant_id']) ?? $payment->tenant_id;

            $payment->paid_at = now();
            return $this->paymentRepository->updatePayment($id, $data);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'error' => ['Erreur lors de la mise à jour du paiement: ' . $e->getMessage()]
            ]);
        }
    }
    //delete paiement
    public function deletePayment(int $id)
    {
        if (auth()->user()->role === 'locataire') {
        throw ValidationException::withMessages([
            'authorization' => 'Action non autorisée.'
        ]);}

        try {
            DB::beginTransaction();

            // Vérifier si le paiement a des reçus associés
            $payment = $this->paymentRepository->findPaymentById($id);
            
            if ($payment->receipts()->exists()) {
                throw ValidationException::withMessages([
                    'payment' => ['Impossible de supprimer un paiement qui a des reçus associés']
                ]);
            }

            $deleted = $this->paymentRepository->deletePayment($id);

            if (!$deleted) {
                throw ValidationException::withMessages([
                    'payment' => ['Erreur lors de la suppression du paiement']
                ]);
            }

            DB::commit();

            return true;

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' => ['Erreur: ' . $e->getMessage()]
            ]);
        }
        
    }
    //search paiement
    public function searchPayments(string $term)
    {
        return $this->paymentRepository->searchPayments($term);
    }

    //all receipts (selon user)
    public function getAllReceipts()
    {
        $role = auth()->user()->role;
        $userId = auth()->user()->id;

        // Si gestionnaire : reçus générés par lui
        if ($role === 'gestionnaire' || $role === 'admin') {
            return $this->receiptRepository->getByGenerator($userId);
        }
        
        // Si locataire : ses propres reçus
        if ($role === 'locataire') {
            return $this->receiptRepository->getByTenant($userId);
        }

      
    }
    //update receipt
    public function updateReceipt(int $id, array $data)
    {
        if (auth()->user()->role === 'locataire') {
        throw ValidationException::withMessages([
            'authorization' => 'Action non autorisée.'
        ]);}

        $updated= $this->receiptRepository->findById($id);
        if (!$updated) {
            throw ValidationException::withMessages([
                'receipt' => ['Reçu introuvable.']
            ]);
        }

        $this->receiptRepository->deleteByReceipt($id);
        foreach ($data['periods'] as $period) {
            $this->receiptRepository->createReceiptPeriod([
                'receipt_id' => $id,
                'period_start' => $period['period_start'],
                'period_end' => $period['period_end']
            ]);
        }

        $updated = $this->receiptRepository->update($id, $data);
        
        return $updated;
    }

    //delete receipt
    public function deleteReceipt(int $id)
    {
        if (auth()->user()->role === 'locataire') {
        throw ValidationException::withMessages([
            'authorization' => 'Action non autorisée.'
        ]);}

        try {
            DB::beginTransaction();

            // Supprimer les périodes associées
            $this->receiptRepository->deleteByReceipt($id);

            // Supprimer le reçu
            $deleted = $this->receiptRepository->delete($id);

            if (!$deleted) {
                throw new \Exception('Reçu non trouvé');
            }

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    //search receipt
    public function searchReceipts(string $term)
    {
        return $this->receiptRepository->search($term);
    }

    public function getPaymentById(int $id)
    {
        return $this->paymentRepository->findPaymentById($id);
    }

    public function getReceiptById(int $id)
    {
        $receipt = $this->receiptRepository->findById($id);
        
        if ($receipt) {
            $receipt->load('periods', 'payment', 'tenant', 'generator');
        }
        
        return $receipt;
    }

    public function getPaymentMethods()
    {
        return $this->paymentRepository->allActive();
    }

    public function getReceiptPeriods(int $receiptId)
    {
        return $this->receiptRepository->getByReceipt($receiptId);
    }
}