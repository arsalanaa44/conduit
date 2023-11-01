<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\enum\TransactionTypeEnum;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'amount', 'action', 'description', 'meta_data'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    protected $casts = [
        'action' => TransactionTypeEnum::class,
    ];

    public function getMetaDataAttribute($value)
    {
        return json_decode($value, true);
    }

    // Define mutator to encode JSON data when setting
    public function setMetaDataAttribute($value)
    {
        $this->attributes['meta_data'] = json_encode($value);
    }
}
