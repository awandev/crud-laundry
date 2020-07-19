<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    // jika fillable akan mengizinkan field apa saja yang ada di dalam arraynya
    // maka guarded akan memblok field apa saja yang ada di dalam arraynya
    // jadi apabila fieldnya banyak maka kita bisa manfaatkan hanya dengan menuliskan array kosong
    // yang berarti tidak ada field yang diblock sehingga semua field tersebut sudah diizinkan
    // hal ini memudahkan kita karena tidak perlu menuliskannya satu per satu
    protected $guarded = [];

    

    // ini adalah accessor, jadi kita membuat kolom baru bernama status_label
    // kolom tersebut dihasilkan oleh accessor, meskipun field tersebut tidak ada di table products
    // akan tetapi akan disertakan pada hasil query
    public function getStatusLabelAttribute()
    {
        // adapun valuenya akan mencetak HTML berdasarkan value dari field status
        if($this->status == 0)
        {
            return '<span class="badge badge-secondary">Draft</span>';
        }
        return '<span class="badge badge-success">Aktif</span>';
    }

    // fungsi yang menghandle relasi ke table category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    
    // sedangkan ini adalah mutators, 
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
}
