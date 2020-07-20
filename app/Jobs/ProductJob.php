<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Imports\ProductImport; //IMPORT CLASS PRODUCTIMPORT YANG AKAN MENG-HANDLE FILE EXCEL
use Illuminate\Support\Str;
use App\Product;
use File;

class ProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;
    protected $filename;

    //KARENA DISPATCH MENGIRIMKAN 2 PARAMETER
    //MAKA KITA TERIMA KEDUA DATA TERSEBUT
      
    public function __construct($category, $filename)
    {
        $this->category = $category;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

     public function handle()
     {
        //  kemudian kita gunakan productimport yang merupakan class yang akan dibuat selaanjutnya
        // import data excel tadi yang sudah disimpan di storage, kemudian convert menjadi array
        $files = (new ProductImport)->toArray(storage_path('app/public/uploads/'. $this->filename));

        // kemudian looping datanya
        foreach($files[0] as $row)
        {
            // formatting urlnya akan mengambil file-namenya beserta extension
            // jadi pastikan pada template mass uploadnya nanti pada bagian url
            // harus diakhiri dengan nama file yang lengkap dengan extension
            $explodeURL = explode('/', $row[4]);
            $explodeExtension = explode('.', end($explodeURL));
            $filename = time() . Str::random(6) . '.' . end($explodeExtension);

            // download gambar tersebut dari URL terkait
            file_put_contents(storage_path('app/public/products') . '/' . $filename, file_get_contents($row[4]));

            // kemudian simpan datanya di database
            Product::create([
                'name'  => $row[0],
                'slug'  => $row[0],
                'category_id'   => $this->category,
                'description'   => $row[1],
                'price'         => $row[2],
                'weight'        => $row[3],
                'image'         => $filename,
                'status'        => true
            ]);
        }

        // jika prosesnya sudah selesai maka file yang ada di storage akan dihapus
        File::delete(storage_path('app/public/uploads/' . $this->filename));
     }

}

?>