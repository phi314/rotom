<?php

namespace App;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'pay', 'is_downpayment', 'is_accepted'
    ];

    public function wash()
    {
        return $this->belongsTo('App\Invoice');
    }

    public function admin_user()
    {
        return $this->belongsTo('App\Admin_user');
    }

    public function getCreatedAtAttribute($value) {
        return strftime('%A, %d %B %Y %k:%M', strtotime($value));
    }

    public static function getLastTransactionPay($invoice_id) {
        $last_transaction = Transaction::where('invoice_id', $invoice_id)->where('is_accepted', true)->orderBy('id', 'desc')->first();

        return !empty($last_transaction) ? $last_transaction->pay : 0;
    }

    public function save(array $options = [])
    {
        $this->attributes['admin_user_id'] = Admin::user()->id;

        parent::save();
    }
}
