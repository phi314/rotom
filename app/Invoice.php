<?php

namespace App;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    public function wash()
    {
        return $this->belongsTo('App\Wash');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function admin_user()
    {
        return $this->belongsTo('App\Admin_user');
    }

    public function getCreatedAtAttribute($value) {
        return strftime('%A, %d %B %Y %k:%M', strtotime($value));
    }

    public function save(array $options = [])
    {
        $this->attributes['admin_user_id'] = Admin::user()->id;

        return parent::save();
    }
}
