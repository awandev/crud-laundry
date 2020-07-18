<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// load model category
use App\Category;
class CategoryController extends Controller
{
    public function index()
    {
        // buat query ke database menggunakan model category dengan mengurutkan berdasarkan created_at dan diset descending, lalu paginate(10)
        // gunakan fungsi WITH() atau EAGER LOADING
        // adapun nama yang disebutkan di dalamnya adalah nama method yang didefinisikan di dalam model category
        // method tersebut berisi fungsi relationships antar table
        // jika lebih dari 1 maka dapat dipisahkan dengan koma,
        // contoh: with(['parent','contoh1','contoh2'])
        $category = Category::with(['parent'])->orderBy('created_at','DESC')->paginate(10);

        // query ini mengambil semua list category dari table categories, perhatikan akhirannya adalah GET() tanpa ada limit
        // lalu getParent() dari mana ? method tersebut adalah sebuah local scope
        $parent = Category::getParent()->orderBy('name','ASC')->get();

        // load view dari folder categories, dan di dalamnya ada file index.blade.php
        // kemudian passing data dari variabel $category dan $parent ke view agar dapat digunakan pada view tersebut
        return view('categories.index', compact('category','parent'));
    }

    public function store(Request $request)
    {
        // jadi kita validasi data yang diterima, dimana name name category wajib diisi
        // tipenya ada string dan max karakternya adalah 50 dan bersifat unik
        $this->validate($request, [
            'name'  => 'required|string|max:50|unique:categories'
        ]);

        // field slug akan ditambahkan ke dalam collection request
        $request->request->add(['slug' => $request->name]);

        // sehingga pada bagian ini kita tinggal menggunakan $request->except()
        // yakni menggunakan semua data yang ada di dalam $request kecuali index _token
        // fungsi request ini secara otomatis akan menjadi array
        // category::create adalah mass assigment untuk memberikan instruksi ke model agar menambahkan data ke tabel terkait
        Category::create($request->except('_token'));
        // apabila berhasil, maka redirect ke halaman list kategori 
        // dan buat flash session menggunakan with()
        // jadi with() disini berbeda fungsinya dengan with() yang disambungkan dengan model
        return redirect(route('category.index'))->with(['success' => 'Kategori Baru Ditambahkan!']);
    }

    public function edit($id)
    {
        // query untuk mengambil data berdasarkan ID
        $category = Category::find($id);
        $parent = Category::getParent()->orderBy('name','ASC')->get();

        // load view edit.blade.php pada folder categories
        // dan passing variabel category dan parent
        return view('categories.edit', compact('category','parent'));

    }

    public function update(Request $request, $id)
    {
        // validasi field name
        // yang berbeda ada tambahan pada rule unique
        // formatnya adalah unique:nama_table, nama_field, id_ignore
        // jadi kita tetap mengecek untuk memastikan bahwa nama categorinya unik
        // akan tetapi khusus data dengan id y ang akan diupdate datanya dikecualikan
        $this->validate($request, [
            'name'  => 'required|string|max:50|unique:categories,name,' .$id
        ]);

        $category = Category::find($id);

        $category->update([
            'name'  => $request->name,
            'parent_id' => $request->parent_id
        ]);

        // redirect ke halaman list kategori
        return redirect(route('category.index'))->with(['success' => 'Kategori Diperbarui']);
    }

    public function destroy($id)
    {
        // buat query untuk mengambil category berdasarkan id menggunakan method find()
        // adapun withCount() serupa dengan eager loading yang menggunakan WITH()
        // hanya saja withCount() returnnya adalah integer
        // jadi nanti hasil querynya akan menambah field baru bernama child_count yang berisi jumlah data anak kategori
        $category = Category::withCount(['child'])->find($id);
        // jika kategori ini tidak digunakan sebagai parent atau childnya = 0
        if ($category->child_count == 0)
        {
            // maka hapus kategori ini
            $category->delete();
            // dan redirect kembali ke halaman list kategori
            return redirect(route('category.index'))->with(['success' => 'Kategori Dihapus!']);
        }
        // selain itu , maka redirect ke list, tapi flash messagenya error yang berarti kategori sedang digunakan
        return redirect(route('category.index'))->with(['error' => 'Kategori Ini Memiliki Anak Kategori']);
    }


}
