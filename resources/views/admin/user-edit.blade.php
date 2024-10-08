@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>User information</h3>
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
                        <a href="{{ route('admin.category.add') }}">
                            <div class="text-tiny">Users</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit User</div>
                    </li>
                </ul>
            </div>
            <!-- new-category -->
            <d class="wg-box">
                <form class="form-new-user form-style-1" action="{{ route('admin.user.update') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $user->id }}"/>
                    <fieldset class="user">
                        <div class="body-title">User Role <span class="tf-color-1">*</span></div>

                        <div class="col-md-10">
                            <div class="select mt-1">
                                <select class="flex-grow" name="role">
                                    <option>Choose Role</option>
                                    <option value="co_admin" {{ $user->role == 'co_admin' ? 'selected' : '' }}>Co-Admin</option>
                                    <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
