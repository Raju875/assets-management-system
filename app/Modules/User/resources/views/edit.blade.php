@extends('layouts.master')

@section('header-resource')
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            @include('message.message')
            <div class="row">
                <div class="col-12">
                    <div class="card card-info mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus"></i> Edit {{ $page_title }}
                            </h3>
                        </div>

                        <form method="POST" action="{{ route('user-store') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_id" value="{{ $user_by_id->id }}">

                            <div class="card-body">
                                <div class="row">
                                    {{--Name--}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name <span class='required-star'></span> </label>
                                            <input type="text" class="form-control" id="name" name="name" required
                                                   value="{{ $user_by_id->name }}"
                                                   placeholder="Enter user name">
                                            @if ($errors->has('name'))<span
                                                class="help-block text-danger">{{ $errors->first('name') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                    <!--Email-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email <span class='required-star'></span> </label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                   value="{{ $user_by_id->email }}"
                                                   placeholder="Enter email">
                                            @if ($errors->has('email'))<span
                                                class="help-block text-danger">{{ $errors->first('email') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!--Department-->
                                    @if($user_by_id->role_id != 1)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Department <span class='required-star'></span> </label>
                                                <select name="dept_id" class="form-control" id="dept_id" required>
                                                    @foreach($departments as $key => $dept)
                                                        <option value="{{ $key }}"
                                                                @if ($user_by_id->dept_id == $key) selected @endif>{{ $dept }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('dept_id'))<span
                                                    class="help-block text-danger">{{ $errors->first('dept_id') }}</span>
                                                <br>@endif
                                            </div>
                                        </div>
                                @endif
                                <!-- status-->
                                    <div class="col-md-6">
                                        <div class="form-group" style="padding-top: 30px">
                                            <label>Status</label>
                                            <div class="icheck-primary d-inline" style="padding-left: 45px">
                                                <input type="radio" id="status1" name="status" value="1"
                                                       {{ ($user_by_id->status == 1) ? 'checked' : '' }}
                                                       required>
                                                <label for="status1">Active</label>
                                            </div>
                                            <div class="icheck-primary d-inline" style="padding-left: 10px">
                                                <input type="radio" id="status2" name="status" value="0"
                                                    {{ ($user_by_id->status == 0) ? 'checked' : '' }}>
                                                <label for="status2">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('user-list') }}">
                                    <button type="button" class="btn btn-danger">Close</button>
                                </a>
                                <button type="submit" class="btn btn-info float-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
