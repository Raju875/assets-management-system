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
                        <form method="POST" action="{{ route('allocate-assign-asset-store') }}" id="assetAssignForm">
                            <div class="card-header">
                                <div class="fa-pull-left">
                                    <h3 class="card-title">
                                        <i class="fas fa-list"></i> Assign asset
                                    </h3>
                                </div>
                                <div class="fa-pull-right">
                                    <button type="submit" id="action_btn" name="actionBtn" class="btn btn-info btn-sm">
                                        <i class="fa fa-plus"></i> Store
                                    </button>
                                    <a href="{{ route('allocate-list') }}" id="back_to_list"
                                       class="btn btn-danger btn-sm">
                                        <i class="fas fa-undo"></i> Back to list
                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            {{--Department--}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Department <span class='required-star'></span> </label>
                                                    <select name="dept_id" class="form-control" id="dept_id"
                                                            onchange="selectDepartment()" required>
                                                        @foreach($departments as $key => $dept)
                                                            <option value="{{ $key }}"
                                                                    @if (old('dept_id') == $key) selected @endif>{{ $dept }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('dept_id'))<span
                                                        class="help-block text-danger">{{ $errors->first('dept_id') }}</span>
                                                    <br>@endif
                                                </div>
                                            </div>
                                            {{--User--}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>User <span class='required-star'></span></label>
                                                    <select name="user_id" class="form-control"
                                                            id="user_id" required>
                                                        <option value="">Choose department first</option>
                                                    </select>
                                                    @if ($errors->has('user_id'))<span
                                                        class="help-block text-danger">{{ $errors->first('user_id') }}</span>
                                                    <br>@endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <table id="list" class="table table-bordered table-striped nowrap">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="all_checked_unchecked">
                                                    <label for="all_checked_unchecked">All</label>
                                                </th>
                                                <th>Asset(Code)</th>
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
                        </form>
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
        var data_table = '';
        $(function () {
            data_table = $('#list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 100,
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
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'asset', name: 'asset'},
                    {data: 'cat_name', name: 'cat_name'},
                    {data: 'sub_cat_name', name: 'sub_cat_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "aaSorting": []
            });
        });

        // ajax request to fetch department wise user
        function selectDepartment() {
            var dept_id = $('#dept_id').val();

            if (dept_id == '') {
                toastr.error('Something went wring! Try again.')
                return false;
            }

            $('#dept_id').after('<span class="loading_data text-danger">Loading...</span>');
            var self = $('#dept_id');

            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "post",
                url: '{{route("allocate-user-by-department")}}',
                data: {
                    _token: _token,
                    dept_id: dept_id
                },
                dataType: 'json',
                success: function (response) {

                    if (response.success == false) {
                        toastr.error(response.message, 'Error!')

                    } else if (response.error == true) {
                        toastr.error(response.message, 'Error!')

                    } else if (response.success == true) {
                        toastr.success(response.message, 'Success')
                        var option = '<option value="">-- Select user --</option>';

                        $.each(response.data, function (id, value) {
                            option += '<option value="' + id + '">' + value + '</option>';
                        });

                        $("#user_id").html(option);

                    } else {
                        toastr.error('Unknown error occurred! Try again', 'Error!')
                    }
                    $(self).next().hide();
                }
            });
        }

        $('#all_checked_unchecked').change(function () {
            if ($('#all_checked_unchecked').is(':checked')) {
                $('input:checkbox').attr('checked', 'checked');
            } else {
                $('input:checkbox').removeAttr('checked');
            }
        })

        // submit form after assigned items
        $('#assetAssignForm').submit(function (event) {

            var check = $('#assetAssignForm').find('input[type=checkbox]:checked').length;
            if ($('#all_checked_unchecked').is(':checked')) {
                check--;
            }
            if (check < 1) {
                toastr.error('Please select at least one item and then add!')
                return false;
            }
            if (!confirm('You have selected ' + check + ' items. Are you sure to add these!')) {
                return false;
            }

            var form = $("#assetAssignForm"); //Get Form ID
            var url = form.attr("action"); //Get Form action
            var type = form.attr("method"); //get form's data send method

            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: type,
                url: url,
                data: {
                    _token: _token,
                    form_data: form.serialize(),
                },
                dataType: 'json',
                beforeSend: function (msg) {
                    $("#action_btn").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                    $("#action_btn").prop('disabled', true); // disable button
                },
                success: function (data) {
                    //==========validation error===========//
                    if (data.success == false || data.error == true) {
                        toastr.error(data.message);
                    }
                    //==========if data is saved=============//
                    if (data.success == true) {
                        data_table.ajax.reload();
                        toastr.info(data.message);
                    }

                    $('input:checkbox').removeAttr('checked');
                    $('#dept_id').val('');
                    $('#user_id').html(' <option value="">Choose department first</option>');

                    $("#action_btn").html('<i class="fa fa-plus"></i> Store');
                    $("#action_btn").prop('disabled', false);
                }
            });
            event.preventDefault();
        });
    </script>
@endsection
