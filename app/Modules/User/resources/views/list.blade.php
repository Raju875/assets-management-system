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
                                    <i class="fas fa-list"></i> {{ $page_title }} List
                                </h3>
                            </div>
                            @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
                                <div class="fa-pull-right">
                                    <a class="" href="{{ route('user-add') }}">
                                        <button class="btn btn-info"><i class="fa fa-plus"></i><b> Add</b></button>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="list" class="table table-bordered table-striped nowrap" cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th width="10%">SL</th>
                                    <th width="25%">User Info</th>
                                    <th width="35%">Asset</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Action</th>
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
                    url: '{{route("user-get-list")}}',
                    method: 'get',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'user_info', name: 'user_info'},
                    {data: 'asset', name: 'asset'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });
    </script>
@endsection
