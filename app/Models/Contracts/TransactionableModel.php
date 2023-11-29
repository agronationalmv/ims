<?php

namespace App\Models\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

abstract class TransactionableModel extends Model{
    public function getProductId():int{
        return $this->product_id;
    }

    public function getQty():int{
        return $this->qty;
    }

    public function getOwner():int{
        return auth()->id();
    }

    public abstract function getTransactionType();

    public static function postTransaction($model,$transaction_type=null){
        Transaction::create([
            'product_id'=>$model->getProductId(),
            'qty'=>$model->getQty(),
            'user_id'=>$model->getOwner(),
            'transaction_type'=>$transaction_type?$transaction_type:$model->getTransactionType(),
            'transactionable_type'=>get_class($model),
            'transactionable_id'=>$model->getKey(),
        ]);
    }

    protected static function boot(){
        parent::boot();
        self::created(function($model){
            self::postTransaction($model);
        });
    }

}