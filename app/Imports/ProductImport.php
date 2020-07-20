<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements WithStartRow, WithChunkReading
{
    /**
    * @param Collection $collection
    */

    use Importable;

    // jadi kita batasi ata yang akan digunakan mulai dari baris kedua, karena baris pertama digunakan sebagai heading 
    // agar memudahkan orang yang mengisi data pada file excel
    public function startRow(): int 
    {
        return 2;
    }

    // kemudian kita gunakan chunkSize untuk mengontrol penggunaan memory dengan membatasi load data dalam sekali proses
    public function chunkSize(): int
    {
        return 100;
    }

   
}
