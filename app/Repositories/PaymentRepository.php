<?php
namespace App\Repositories;
use Illuminate\Support\Facades\Auth;

use App\Models\Payment;
use App\Models\PaymentMethod;

class PaymentRepository
{
    //payment method
    // les methodes de paiement actives
    public function allActive()
    {
        return PaymentMethod::where('is_active', true)->get();
    }
    // create
    public function create(array $data)
    {
        return PaymentMethod::create($data);
    }
    //update
    public function update(int $id, array $data): bool
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return false;
        }

        return $paymentMethod->update($data);
    }
    //delete
    public function delete(int $id): bool
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return false;
        }

        return $paymentMethod->delete();
    }
    //getall
    public function getAll()
    {
        return PaymentMethod::all();
    }
    //find by code
    public function findByCode(string $code): ?PaymentMethod
    {
        return PaymentMethod::where('code', $code)->first();
    }

    //payment
    //create
    public function createPayment(array $data)
    {
        return Payment::create($data);
    }
    //update
    public function updatePayment(int $id, array $data): bool
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return false;
        }
        return $payment->update($data);
    }
    //find by id
    public function findPaymentById(int $id): ?Payment
    {
        return Payment::find($id);
    }
    //delete
    public function deletePayment(int $id): bool
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return false;
        }
        return $payment->delete();
    }
    //payments by tenant
    public function getPaymentsByTenant(int $tenantId, int $perPage = 10)
    {
        return Payment::where('tenant_id', $tenantId)->orderBy('paid_at', 'desc')->paginate($perPage);
    }
    //payments by manager
    public function getPaymentsByManager(int $managerId, int $perPage = 10)
    {
        return Payment::where('manager_id', $managerId)->orderBy('paid_at', 'desc')->paginate($perPage);
    }
    //all
    public function getAllPayments(int $perPage = 10)
    {
        return Payment::orderBy('paid_at', 'desc')->paginate($perPage);
    }

    //search
    public function searchPayments(?string $term = null, int $perPage = 10)
    {
        $query = Payment::with(['tenant', 'manager', 'paymentMethod']);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('status', 'LIKE', "%{$term}%")
                  ->orWhere('currency', 'LIKE', "%{$term}%")
                  ->orWhereHas('tenant', function ($tenantQuery) use ($term) {
                      $tenantQuery->where('name', 'LIKE', "%{$term}%")
                                  ->orWhere('surname', 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('manager', function ($managerQuery) use ($term) {
                      $managerQuery->where('name', 'LIKE', "%{$term}%")
                                   ->orWhere('surname', 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('paymentMethod', function ($pmQuery) use ($term) {
                      $pmQuery->where('name', 'LIKE', "%{$term}%");
                  });
            });
        }

        return $query->orderBy('paid_at', 'desc')->paginate($perPage);
    }

}