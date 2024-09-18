@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add Product</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.products') }}">
                            <div class="text-tiny">Products</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit Product</div>
                    </li>
                </ul>
            </div>
            <!-- form-add-product -->
            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $product->id }}"/>
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter product name"
                               name="name" tabindex="0" value="{{ $product->name }}" aria-required="true" required="">
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error('name')
                    <span class="alert alert-danger text-center">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="gap22 cols">
                        <fieldset class="category">
                            <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
                            </div>
                            <div class="select">
                                <select class="" name="category_id">
                                    <option>Choose category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="brand">
                            <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
                            </div>
                            <div class="select">
                                <select class="" name="brand_id">
                                    <option>Choose Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <fieldset class="description">
                        <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                        </div>
                        <textarea class="mb-10" name="description" placeholder="Description"
                                  tabindex="0" aria-required="true" required="">{{ $product->description }}</textarea>
                        <div class="text-tiny">Do not exceed 100 characters when entering the product description.</div>
                    </fieldset>
                    @error('description')
                    <span class="alert alert-danger text-center">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">UPC <span class="tf-color-1">*</span>
                            </div>
                            <input class="mb-10" type="text" placeholder="Enter UPC" name="UPC"
                                   tabindex="0" value="{{ $product->UPC }}" aria-required="true" required="">
                        </fieldset>
                        @error('UPC')
                        <span class="alert alert-danger text-center">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="wg-box">
                    <fieldset>
                        <div class="body-title">Upload images <span class="tf-color-1">*</span>
                        </div>
                        <div class="upload-image flex-grow">
                            @if($product->image)
                                <div class="item" id="imgpreview" style="display:none">
                                    <img src="{{ asset('upload/products') }}/{{ $product->image }}" class="effect8" alt="{{ $product->name }}">
                                </div>
                            @endif
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                                        <span class="icon">
                                                            <i class="icon-upload-cloud"></i>
                                                        </span>
                                    <span class="body-text">Drop your images here or select <span
                                            class="tf-color">click to browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    @error('image')
                    <span class="alert alert-danger text-center">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <fieldset>
                        <div class="body-title mb-10">Upload Gallery Images</div>
                        <div class="upload-image mb-16">
                            @if($product->images)
                                @foreach(explode(',', $product->images) as $img)
                                    <div class="item gitems">
                                        <img src="{{ asset('upload/products') }}/{{ trim($img) }}" alt="{{ $product->name }}">
                                    </div>
                                @endforeach
                            @endif
                            <div id="galUpload" class="item up-load">
                                <label class="uploadfile" for="gFile">
                                                        <span class="icon">
                                                            <i class="icon-upload-cloud"></i>
                                                        </span>
                                    <span class="text-tiny">Drop your images here or select <span
                                            class="tf-color">click to browse</span></span>
                                    <input type="file" id="gFile" name="images[]" accept="image/*"
                                           multiple="">
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Regular Price <span
                                    class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter regular price"
                                   name="regular_price" tabindex="0" value="{{ $product->regular_price }}" aria-required="true"
                                   required="">
                        </fieldset>
                        @error('regular_price')
                        <span class="alert alert-danger text-center">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <fieldset class="name">
                            <div class="body-title mb-10">Sale Price <span
                                    class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter sale price"
                                   name="sales_price" tabindex="0" value="{{ $product->sales_price }}" aria-required="true"
                                   required="">
                        </fieldset>
                        @error('sales_price')
                        <span class="alert alert-danger text-center">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Featured</div>
                            <div class="select mb-10">
                                <select class="" name="is_active">
                                    <option value="0" {{ $product->is_active == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $product->is_active == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Is New</div>
                            <div class="select mb-10">
                                <select class="" name="is_new_arrival">
                                    <option value="0" {{ $product->is_new_arrival == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $product->is_new_arrival == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>
                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Save</button>
                    </div>
                </div>
            </form>
            <!-- /form-add-product -->
        </div>
        <!-- /main-content-wrap -->
    </div>
@endsection

@push('scripts')
    <script>
        $(function (){
            $("#myFile").on("change", function (e){
                const photoInp = $("#myFile");
                const [file] = this.files;
                if(file)
                {
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });
            $("input[name='name']").on('change', function (){
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });
        });

        function StringToSlug(Text)
        {
            return Text.toLowerCase()
                .replace(/ +/g, "-")
                .replace(/[^\w-]+/g, "");
        }
    </script>
@endpush
