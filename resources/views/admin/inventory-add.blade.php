@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Inventory information</h3>
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
                        <a href="{{ route('admin.inventories') }}">
                            <div class="text-tiny">Inventories</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">New Inventory</div>
                    </li>
                </ul>
            </div>
            <!-- new-category -->
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('admin.inventory.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <fieldset class="name">
                        <div class="body-title">Quantity <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="number" placeholder="Quantity" name="quantity"
                               tabindex="0" value="{{ old('quantity') }}" aria-required="true" required="">
                    </fieldset>
                    @error('quantity')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Reorder Level <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="number" placeholder="Reorder Level" name="reorder_level"
                               tabindex="0" value="{{ old('reorder_level') }}" aria-required="true" required="">
                    </fieldset>
                    @error('reorder_level')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Last Rorder Date <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" id="datetime" type="text" placeholder="Last Rorder Date" name="last_reorder_date"
                               tabindex="0" value="{{ old('last_reorder_date') }}" aria-required="true" required="">
                    </fieldset>
                    @error('last_reorder_date')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror

                    <div class="gap22 cols">
                        <fieldset class="product">
                            <div class="body-title mb-10">Product <span class="tf-color-1">*</span>
                            </div>
                            <div class="select">
                                <select class="flex-grow" name="product_id">
                                    <option value="{{ null }}">Choose Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="warehouse">
                            <div class="body-title mb-10">Warehouse <span class="tf-color-1">*</span>
                            </div>
                            <div class="select">
                                <select class="flex-grow" name="warehouse_id">
                                    <option value="{{ null }}">Choose Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#datetime", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
    </script>
@endpush
