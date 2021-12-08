@extends('layouts.master')

@section('header-resource')
    @include('vendor.select2.select2_css')
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: #343a40;
            background-color: #ffffff;
            border: 1px solid #bdc6d0;;
        }
    </style>
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
                                <i class="fas fa-plus"></i> Add {{ $page_title }}
                            </h3>
                        </div>

                        <form method="POST" action="{{ route('asset-category-store') }}">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="row">
                                    {{--Category Name--}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category Name <span class='required-star'></span> </label>
                                            <input type="text" class="form-control" id="name" name="name" required
                                                   value="{{ old('name') }}"
                                                   placeholder="Enter category name">
                                            @if ($errors->has('name'))<span
                                                class="help-block text-danger">{{ $errors->first('name') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                    {{--Sub Category Name--}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sub Category Name</label>
                                            <select multiple="true" name="sub_cat_name[]" id="sub_cat_name"
                                                    class="form-control select2"></select>
                                            @if ($errors->has('sub_cat_name'))<span
                                                class="help-block text-danger">{{ $errors->first('sub_cat_name') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    {{--status--}}
                                    <div class="col-md-6">
                                        <div class="form-group" style="padding-top: 30px">
                                            <label>Status</label>
                                            <div class="icheck-primary d-inline" style="padding-left: 45px">
                                                <input type="radio" id="status1" name="status" value="1" checked
                                                       required>
                                                <label for="status1">Active</label>
                                            </div>
                                            <div class="icheck-primary d-inline" style="padding-left: 10px">
                                                <input type="radio" id="status2" name="status" value="0">
                                                <label for="status2">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('asset-category-list') }}">
                                    <button type="button" class="btn btn-danger">Close</button>
                                </a>
                                <button type="submit" class="btn btn-info float-right">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    @include('vendor.select2.select2_js')

    <script>
        $(function () {
            $('#sub_cat_name').select2({
                tags: true
            });
        })
    </script>
@endsection
