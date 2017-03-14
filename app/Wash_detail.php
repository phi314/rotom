<?php

namespace App;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class Wash_detail extends Model
{
    protected $table = 'wash_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id', 'qty', 'notes'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wash()
    {
        return $this->belongsTo('App\Wash');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function getCreatedAtAttribute($value) {
        return strftime('%A, %d %B %Y %k:%M', strtotime($value));
    }

    public function save(array $options = [])
    {
        $this->setPriceAttribute();
        $this->setSubtotalAttribute();

        parent::save();
    }

    /**
     * Set Price
     */
    public function setPriceAttribute()
    {
        $item = Item::find($this->item_id);
        $this->attributes['price'] = $item->price;
    }

    /**
     * Set Subtotal
     */
    public function setSubtotalAttribute()
    {
        $this->attributes['subtotal'] = $this->price * $this->qty;
    }
}
