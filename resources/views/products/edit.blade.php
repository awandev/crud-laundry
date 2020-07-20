@extends('layouts.admin')

@section('title')
    <title>Edit Produk</title>
@endsection

@section('content')
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Product</li>
        </ol>
        <div class="container-fluid">
            <div class="animated fadeIn">
                {{-- pastikan mengirimkan ID pada route yang digunakan --}}
                <form action="{{ route('product.update', $product->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- karena update maka kita gunakan directive PUT --}}
                    @method('PUT')

                    {{-- form ini sama dengan create, yang berbeda hanya ada tambahan value untuk masing2 inputan --}}
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Produk</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Nama Produk</label>
                                        <input type="text" name="name" id="name" value="{{ $product->name }}" class="form-control" required>
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Deskripsi</label>
                                        <textarea name="description" id="description" class="form-control">{{ $product->description }}</textarea>
                                        <p class="text-danger">{{ $errors->first('description') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="1" {{ $product->status == '1' ? 'selected':''}}>Publish</option>
                                            <option value="0" {{ $product->status == '0' ? 'selected':''}}>Draft</option>
                                        </select>
                                        <p class="text-danger">{{ $errors->first('status') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Kategori</label>
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="">Pilih</option>
                                            @foreach ($category as $row)
                                            <option value="{{ $row->id }}" {{ $product->category_id == $row->id ? 'selected': ''}}>{{ $row->name }}</option> 
                                            @endforeach
                                        </select>
                                        <p class="text-danger">{{ $errors->first('category_id') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Harga</label>
                                        <input type="number" name="price" id="price" class="form-control" value="{{ $product->price }}" required>
                                        <p class="text-danger">{{ $errors->first('price') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Berat</label>
                                        <input type="number" name="weight" id="weight" class="form-control" value="{{ $product->weight }}" required>
                                        <p class="text-danger">{{ $errors->first('weight') }}</p>
                                    </div>

                                    {{-- gambar tidak lagi wajib, jika diisi maka gambar akan diganti, jika dibiarkan kosong maka gambar tidak akan diupdate --}}
                                    <div class="form-group">
                                        <label for="image">Foto Produk</label>
                                        <br>
                                        {{-- tampilkan gambar saat ini --}}
                                        <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" width="100px" height="100px">
                                        
                                        <hr>
                                        <input type="file" name="image" id="image" class="form-control">
                                        <p><strong>Biarkan Kosong Jika tidak ingin mengganti gambar</strong></p>
                                        <p class="text-danger">{{ $errors->first('image') }}</p>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm">Update</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>
@endsection