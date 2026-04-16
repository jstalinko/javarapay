<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'province',
        'city',
        'postal_code',
        'limit_project',
        'limit_bank',
        'withdraw_fee_type',
        'withdraw_fee',
        'fee_tx_type',
        'fee_tx',
        'role',
        'status'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /**
     * Get the available balance of the user.
     * Settled transactions total minimum withdrawal amount.
     *
     * @return int
     */
    public function getAvailableBalance(): int
    {
        $saldo = Transaction::whereHas('project', function($q) {
            $q->where('user_id', $this->id);
        })->whereNotNull('settled_at')->sum('total_amount');

        $withdrawals = Withdrawal::where('user_id', $this->id)
            ->whereIn('status', ['PAID', 'PENDING'])
            ->sum('amount'); // amount is the total deduction

        return (int) ($saldo - $withdrawals);
    }

    /**
     * Calculate withdrawal fee based on user's fee type and fee value.
     *
     * @param int $amount
     * @return int
     */
    public function calculateWithdrawalFee(int $amount): int
    {
        if ($this->withdraw_fee_type === 'percent') {
            return (int) (($amount * $this->withdraw_fee) / 100);
        }

        return (int) $this->withdraw_fee;
    }


    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }
    public function getLimitProject()
    {
        $limit_project = $this->limit_project;
        $total_project = $this->projects()->count();
        if($total_project >= $limit_project){
            return $limit_project;
        }
        return $limit_project - $total_project;
    }

    public function getLimitBank()
    {
        $limit_bank = $this->limit_bank;
        $total_bank = $this->banks()->count();
        if($total_bank >= $limit_bank){
            return $limit_bank;
        }
        return $limit_bank - $total_bank;
    }
}
