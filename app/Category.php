<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name','parent_id','slug'];
    // ini adalah method untuk menghandle relationships
    public function parent()
    {
        // karena relasinya dengan dirinya sendiri, maka class model di dalam belongsTo() adalah nama classnya sendiri yaitu category
        // belongsTo digunakan untuk refleksi ke data induknya
        return $this->belongsTo(Category::class);
    }

    // untuk local scope nama methodnya diawal dengan kata scope diganti dengan nama method yang diinginkana
    // contoh : scopeNamaMethod()
    public function scopeGetParent($query) 
    {
        // semua query yang menggunakan local scope ini akan secara otomatis ditambahkan kondisi whereNul('parent_id')
        return $query->whereNull('parent_id');
    }

    // mutator
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    // accessor
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function child()
    {
        // menggunakan relasi one to many dengan foreign key parent_id
        return $this->hasMany(Category::class, 'parent_id');
    }
}
