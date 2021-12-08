@extends('layouts.master')

@section('header-resource')
    @include('vendor.datatable.datatable_css')
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
                                    <i class="fas fa-list"></i> {{ $page_title }} info
                                </h3>
                            </div>
                            <div class="fa-pull-right">
                                <a class="" href="{{ route('asset-add') }}">
                                    <button class="btn btn-info"><i class="fa fa-plus"></i><b> Add</b></button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="list" class="table table-bordered table-striped nowrap" cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Asset</th>
                                    <th>List</th>
                                    <th>Category</th>
                                    <th>Sub category</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    @include('vendor.datatable.datatable_js')

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <script>
        $(function () {
            $('#list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 10,
                responsive: true,
                ajax: {
                    url: '{{route("asset-get-list")}}',
                    method: 'post',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'tracking_code', name: 'tracking_code'},
                    {data: 'cat_name', name: 'cat_name'},
                    {data: 'sub_cat_name', name: 'sub_cat_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });
    </script>

@endsection
