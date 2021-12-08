@extends('layouts.master')

@section('header-resource')
    @include('vendor.datatable.datatable_css')
    @include('vendor.toastr.toastr_css')
@endsection

@section('content')

    <section class="content">
        <div class="container-fluid">
            @include('message.message')
            <div class="row">
                <div class="col-12">
                    <div class="card mt-3">
                        <div class="card-header">
                            <div class="fa-pull-left">
                                <h3 class="card-title">
                                    <i class="fas fa-list"></i> {{ $page_title }} Info
                                </h3>
                            </div>
                            <div class="fa-pull-right">
                                <a class="" href="{{ route('allocate-assign-asset') }}">
                                    <button class="btn btn-info"><i class="fa fa-plus"></i><b> New Assign</b></button>
                                </a>
                            </div>
                        </div>

                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <!--Remaining-->
                                <li class="nav-item"><a class="nav-link active" href="#remaining" id="remaining_tab"
                                                        data-toggle="tab">Remaining</a></li>
                                <!--Assigned-->
                                <li class="nav-item"><a class="nav-link" href="#assigned" id="assigned_tab"
                                                        data-toggle="tab">Assigned</a></li>
                                <!--Department Basis-->
                                <li class="nav-item"><a class="nav-link " href="#department_basis"
                                                        id="department_basis_tab"
                                                        data-toggle="tab">Department basis</a></li>
                                <!--Within 5 days-->
                                <li class="nav-item"><a class="nav-link" href="#time_basis" id="time_basis_tab"
                                                        data-toggle="tab">Within 5 days</a></li>
                            </ul>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                {{--Remaining--}}
                                @include('Allocate::remaining_list')

                                {{--Allocated--}}
                                @include('Allocate::assigned_list')

                                {{--Department Basis--}}
                                @include('Allocate::department_basis_list')

                                {{--Time Basis--}}
                                @include('Allocate::time_basis_list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    @include('vendor.datatable.datatable_js')
    @include('vendor.toastr.toastr_js')

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <script>
        var department_basis_list = '';
        // remaining list
        $('#remaining_tab').click(function () {
            $('#remaining_list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 25,
                retrieve: true,
                responsive: true,
                ajax: {
                    url: '{{route("allocate-get-list")}}',
                    method: 'post',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d._key = 'remaining';
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'asset', name: 'asset'},
                    {data: 'cat_name', name: 'cat_name'},
                    {data: 'sub_cat_name', name: 'sub_cat_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });

        // assigned list
        $('#assigned_tab').click(function () {
            $('#assigned_list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 25,
                retrieve: true,
                responsive: true,
                ajax: {
                    url: '{{route("allocate-get-list")}}',
                    method: 'post',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d._key = 'assigned';
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'asset', name: 'asset'},
                    {data: 'cat_name', name: 'cat_name'},
                    {data: 'sub_cat_name', name: 'sub_cat_name'},
                    {data: 'assign_to', name: 'assign_to'},
                    {data: 'assign_by', name: 'assign_by'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });

        // department basis list
        $('#department_basis_tab').click(function () {
            department_basis_list = $('#department_basis_list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 25,
                retrieve: true,
                responsive: true,
                ajax: {
                    url: '{{route("allocate-get-list")}}',
                    method: 'post',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d._key = 'department_basis';
                        d._dept_id = $('#dept_id').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'asset', name: 'asset'},
                    {data: 'cat_name', name: 'cat_name'},
                    {data: 'sub_cat_name', name: 'sub_cat_name'},
                    {data: 'assign_to', name: 'assign_to'},
                    {data: 'assign_by', name: 'assign_by'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });

        // time basis list
        $('#time_basis_tab').click(function () {
            time_basis_list = $('#time_basis_list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 25,
                retrieve: true,
                responsive: true,
                ajax: {
                    url: '{{route("allocate-get-list")}}',
                    method: 'post',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                        d._key = 'time_basis';
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'asset', name: 'asset'},
                    {data: 'cat_name', name: 'cat_name'},
                    {data: 'sub_cat_name', name: 'sub_cat_name'},
                    {data: 'assign_to', name: 'assign_to'},
                    {data: 'assign_by', name: 'assign_by'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });

        $(function () {
            $('#remaining_tab').trigger("click");
        });

        $('#dept_id').on('change', function () {
            department_basis_list.ajax.reload();
        });
    </script>
@endsection
