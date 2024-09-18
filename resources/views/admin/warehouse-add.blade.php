@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Warehouse information</h3>
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
                        <a href="{{ route('admin.warehouses') }}">
                            <div class="text-tiny">Warehouses</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">New Warehouse</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('admin.warehouse.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <fieldset class="name">
                        <div class="body-title">Warehouse Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Warehouse Name" name="name"
                               tabindex="0" value="{{ old('name') }}" aria-required="true" required="">
                    </fieldset>
                    @error('name')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Warehouse Location <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Warehouse Location" name="location"
                               tabindex="0" value="{{ old('location') }}" aria-required="true" required="">
                    </fieldset>
                    @error('location')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Warehouse Contact Information <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Warehouse Contact Information" name="contact_info"
                               tabindex="0" value="{{ old('contact_info') }}" aria-required="true" required="">
                    </fieldset>
                    @error('contact_info')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
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
