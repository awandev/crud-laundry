<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// load model product
use App\Product; 
Use App\Category;
Use App\Jobs\ProductJob;

use Illuminate\Support\Str;
Use File;
class ProductController extends Controller
{
    public function index()
    {
        // buat query menggunakan model product, dengan mengurutkan data berdasarkan created_at
        // kemudian load table yang berelasi menggunakan eager loading with()
        // adapun category adalah nama fungsi yang nantinya akan ditambahkan pada product model
        $product = Product::with(['category'])->orderBy('created_at', 'DESC');

        // jika terdapat parameter pencarian di url atau q pada url tidak sama dengan kosong
        if(request()->q != '') 
        {
            // maka lakukan filtering data berdasarkan name dan valuenya sesuai dengan pencarian yang dilakukan user
            $product = $product->where('name', 'LIKE', '%' . request()->q . '%');
        }
        // terakhir load 10 data perhalaman
        $product = $product->paginate(10);
        // load view index.blade.php yang berada di dalam folder products
        // dan passing variable $product ke view agar dapat digunakan
        return view('products.index', compact('product'));
    }

    public function create()
    {
        // query untuk mengambil semua data category
        $category = Category::orderBy('name', 'DESC')->get();
        // load view create.blade.php yang berada di folder products
        // dan passing data category
        return view('products.create', compact('category'));
    }

    public function store(Request $request)
    {
        // validasi requestnya
        $this->validate($request, [
            'name'          => 'required|string|max:100',
            'description'   => 'required',
            'category_id'   => 'required|exists:categories,id', //category_id kita cek harus ada di tabel categories dengan field id
            'price'         => 'required|integer',
            'weight'        => 'required|integer',
            'image'         => 'required|image|mimes:png,jpeg,jpg' // gambar divalidasi harus bertipe png, jpg, dan jpeg
        ]);

        // jika filenya ada
        if($request->hasFile('image')) {
            // maka kita simpan sementara file tersebut ke dalam variabel file
            $file = $request->file('image');
            // kemudian nama filenya kita buat customer dengan perpaduan time dan slug dari nama produk
            // adapun extensionnya kita gunakan bawaan file tersebut
            $filename = time(). Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            // simpan filenya ke dalam folder public/products, dan parameter kedua adalah nama custom untuk file tersebut
            $file->storeAs('public/products', $filename);

            // setelah file tersebut disimpan, kita simpana informasi produknya ke dalam database
            $product = Product::create([
                'name'  => $request->name,
                'slug'  => $request->name,
                'category_id'   => $request->category_id,
                'description'   => $request->description,
                'image'         => $filename, //PASTIKAN MENGGUNAKAN VARIABEL YANG HANYA BERISI NAMA FILE SAJA (STRING)
                'price'         => $request->price,
                'weight'        => $request->weight,
                'status'        => $request->status
            ]);
            // jika sudah, maka redirect ke list product
            return redirect(route('product.index'))->with(['success' => 'Produk Baru Ditambahkan']);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id); //query untuk mengambil data produk berdasarkan ID
        // hapus file image dari storage path diikuti dengan nama image yang diambil dari database
        File::delete(storage_path('app/public/products/' . $product->image));
        // kemudian hapus data produk dari database
        $product->delete();
        // dan redirect ke halaman list product
        return redirect(route('product.index'))->with(['success' => 'Produk Sudah Dihapus']);
    }

    public function edit($id)
    {
        $product = Product::find($id); // ambil data produk terkait berdasarkan ID
        $category = Category::orderBy('name', 'DESC')->get(); // ambil semua data kategori
        return view('products.edit', compact('product','category')); // load view dan passing datanya ke view
    }

    public function update(Request $request, $id)
    {
        // validasi data yang dikirim
        $this->validate($request, [
            'name'          => 'required|string|max:100',
            'description'   => 'required',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|integer',
            'weight'        => 'required|integer',
            'image'         => 'nullable|image|mimes:png,jpeg,jpg'  //image bisa nullable
        ]);

        // ambil data produk yang akan diedit berdasarkan id
        $product = Product::find($id);
        // simpan sementara nama file image saat ini
        $filename = $product->image;

        // jika ada file gambar yang dikirim
        if($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();

            // maka upload file tersebut
            $file->storeAs('public/products', $filename);

            // dan hapus file gambar yang lama
            File::delete(storage_path('app/public/products/' . $product->image));
        }

        // lalu update produk tersebut
        $product->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'price'         => $request->price,
            'weight'        => $request->weight,
            'image'         => $filename
        ]);

        // redirect ke halaman produk dan tampilkan session sukses
        return redirect(route('product.index'))->with(['success' => 'Data Produk Diperbaharui']);
    }


    
    public function massUploadForm()
    {
        // untuk upload massal
        $category = Category::orderBy('name','DESC')->get();
        return view('products.bulk', compact('category'));
    }

    public function massUpload(Request $request)
    {
        // validasi data yang dikirim
        $this->validate($request,[
            'category_id'   => 'required|exists:categories,id',
            'file'          => 'required|mimes:xlsx'
        ]);

        // jika filenya ada
        if ($request->hasFile('file'))
        {
            $file = $request->file('file');
            $filename = time() . '-product.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads', $filename); // maka simpan file tersebut di storage/app/public/uploads

            // buat jadwal untuk proses file tersebut dengan menggunakan job
            // adapun pada dispatch kita mengirimkan dua parameter sebagai informasi
            // yakni kategori id dan nama filenya yang sudah disimpan
            ProductJob::dispatch($request->category_id, $filename);
            return redirect()->back()->with(['success' => 'upload Produk dijadwalkan']);
        }

    }
    


}
