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
                        <a href="{{ route('admin.warehouse.add') }}">
                            <div class="text-tiny">Warehouses</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit Warehouse</div>
                    </li>
                </ul>
            </div>
            <!-- new-category -->
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('admin.warehouse.update') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $warehouse->id }}"/>
                    <fieldset class="name">
                        <div class="body-title">Warehouse Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Warehouse name" name="name"
                               tabindex="0" value="{{ $warehouse->name }}" aria-required="true" required="">
                    </fieldset>
                    @error('name')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Warehouse Location <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Warehouse Location" name="location"
                               tabindex="0" value="{{ $warehouse->location }}" aria-required="true" required="">
                    </fieldset>
                    @error('location')
                    <span class="alert alert-danger text-center">
                        {{ $message }}
                    </span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Warehouse Contact Information <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Warehouse Contect Information" name="contact_info"
                               tabindex="0" value="{{ $warehouse->contact_info }}" aria-required="true" required="">
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
