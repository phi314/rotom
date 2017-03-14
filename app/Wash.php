<?php

namespace App;

use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class Wash extends Model
{
    protected $table = 'washes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'user_id', 'total_amount', 'notes', 'status', 'admin_user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wash_details()
    {
        return $this->hasMany('App\Wash_detail');
    }

    public function invoice()
    {
        return $this->hasOne('App\Invoice');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function admin_user()
    {
        return $this->belongsTo('App\Admin_user');
    }

    public function getCreatedAtAttribute($value) {
        return empty($value) ? '' : strftime('%A, %d %B %Y %k:%M', strtotime($value));
    }

    public function getFinishedAtAttribute($value) {
        return empty($value) ? '' : strftime('%A, %d %B %Y %k:%M', strtotime($value));
    }

    /**
     * Get Random number with number list of today washes 2 digit in the middle
     * @return string
     */
    public function getNewCodeAttribute()
    {
        $washes_today_count = Wash::whereDate('created_at', '=', Carbon::today()->toDateString())->count();

        return rand(10, 99).str_pad($washes_today_count + 1, 2, '0', STR_PAD_LEFT).rand(10, 99);
    }

    public function setTotalPriceAttribute()
    {
        $wash_details = Wash_detail::where('wash_id', '=', $this->id)->get();
        $total_price = 0;
        foreach ($wash_details as $wash_detail)
        {
            $total_price += $wash_detail->price * $wash_detail->qty;
        }

        return $total_price;
    }

    public function save(array $options = [])
    {
        $this->attributes['admin_user_id'] = Admin::user()->id;

        parent::save();
    }

    public function setFinishedAtAttribute()
    {
        if ($this->status == 'finish')
        {
            $this->attributes['finished_at'] = Carbon::now();
        }
        else
        {
            $this->attributes['finished_at'] = null;
        }
    }
}
