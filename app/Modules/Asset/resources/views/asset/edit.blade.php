@extends('layouts.master')

@section('header-resource')
    @include('vendor.toastr.toastr_css')
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
                                <i class="fas fa-list"></i> Details {{ $page_title }}
                            </h3>
                        </div>

                        <form method="POST" action="{{ route('asset-store') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="asset_id" value="{{ $asset_by_id->id }}">
                            <div class="card-body">
                                <div class="row">
                                    {{--Name--}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name <span class='required-star'></span> </label>
                                            <input type="text" class="form-control" id="name" name="name" required
                                                   value="{{ $asset_by_id->name }}" disabled
                                                   placeholder="Enter category name">
                                            @if ($errors->has('name'))<span
                                                class="help-block text-danger">{{ $errors->first('name') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                    {{--Quantity--}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quantity <span class='required-star'></span> </label>
                                            <input type="number" class="form-control" id="quantity" name="quantity"
                                                   required min="1" disabled
                                                   value="{{ $asset_by_id->quantity }}">
                                            @if ($errors->has('quantity'))<span
                                                class="help-block text-danger">{{ $errors->first('quantity') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    {{--Category--}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category <span class='required-star'></span> </label>
                                            <select name="cat_id" class="form-control" id="cat_id" disabled
                                                    {{--                                                    onchange="selectCategory()" --}}
                                                    required>
                                                <option
                                                    value="">{{ $asset_by_id['category'] ? $asset_by_id['category']['name'] : '--'}}</option>
                                                {{--                                                @foreach($categories as $key => $category)--}}
                                                {{--                                                    <option value="{{ $key }}"--}}
                                                {{--                                                            @if ($asset_by_id->cat_id == $key) selected @endif>{{ $category }}</option>--}}
                                                {{--                                                @endforeach--}}
                                            </select>
                                            @if ($errors->has('cat_id'))<span
                                                class="help-block text-danger">{{ $errors->first('cat_id') }}</span>
                                            <br>@endif
                                        </div>
                                    </div>
                                    {{--Sub Category--}}
                                    <div class="col-md-6" id="sub_cat_id_div">
                                        <div class="form-group">
                                            <label>Sub Category <span class='required-star'></span></label>
                                            <select name="sub_cat_id" class="form-control" disabled
                                                    id="sub_cat_id">
                                                <option
                                                    value="">{{ $asset_by_id['subCategory'] ? $asset_by_id['subCategory']['name'] : '--'}}</option>
                                            </select>
                                            @if ($errors->has('sub_cat_id'))<span
                                                class="help-block text-danger">{{ $errors->first('sub_cat_id') }}</span>
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
                                                <input type="radio" id="status1" name="status" value="1" disabled
                                                       {{ ($asset_by_id->status == 1) ? 'checked' : '' }}
                                                       required>
                                                <label for="status1">Active</label>
                                            </div>
                                            <div class="icheck-primary d-inline" style="padding-left: 10px">
                                                <input type="radio" id="status2" name="status" value="0"
                                                    {{ ($asset_by_id->status == 0) ? 'checked' : '' }}>
                                                <label for="status2">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('asset-list') }}">
                                    <button type="button" class="btn btn-danger">Close</button>
                                </a>
                                {{--                                <button type="submit" class="btn btn-info float-right">Update</button>--}}
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Asset List
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Tracking code</th>
                                    <th>Is assigned</th>
                                    <th>Updated by</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($asset_by_id['assetList'])
                                    @foreach($asset_by_id['assetList'] as $key=>$list)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $list['tracking_code'] }}</td>
                                            <td>{{ ($list['is_assign'] == 1) ? 'Yes' : 'No'}}</td>
                                            <td>{{ $asset_by_id['updatedByUser'] ? $asset_by_id['updatedByUser']['name'] : '-'}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('asset-list') }}">
                                <button type="button" class="btn btn-danger">Close</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    @include('vendor.toastr.toastr_js')

    <script>
        // ajax request to fetch sub-categories by select of category
        function selectCategory() {
            var cat_id = $('#cat_id').val();

            if (cat_id == '') {
                $('#sub_cat_id_div').hide('slow');
                $('#sub_cat_id').removeClass('required');
                $('#sub_cat_id').html('');
                return false;
            }

            $('#cat_id').after('<span class="loading_data text-danger">Loading...</span>');
            var self = $('#cat_id');

            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "post",
                url: '{{route("asset-sub-category-by-category")}}',
                data: {
                    _token: _token,
                    cat_id: cat_id
                },
                dataType: 'json',
                success: function (response) {

                    if (response.success == false) {
                        $('#sub_cat_id_div').hide('slow');
                        $('#sub_cat_id').removeClass('required');
                        $('#sub_cat_id').html('');

                    } else if (response.error == true) {
                        $('#sub_cat_id_div').hide('slow');
                        $('#sub_cat_id').removeClass('required');
                        $('#sub_cat_id').html('');

                        toastr.error(response.message, 'Error!')

                    } else if (response.success == true) {
                        toastr.success(response.message, 'Success')
                        var option = '<option value="">-- Choose sub category --</option>';

                        var selected = '';
                        $.each(response.data, function (id, value) {
                            selected = ("{{ $asset_by_id->sub_cat_id }}" == id) ? 'selected' : '';
                            option += '<option value="' + id + '" ' + selected + '>' + value + '</option>';
                        });

                        $('#sub_cat_id_div').show('slow');
                        $('#sub_cat_id').addClass('required');
                        $("#sub_cat_id").html(option);

                    } else {
                        toastr.error('Unknown error occurred! Try again', 'Error!')
                    }
                    $(self).next().hide();
                }
            });
        }
    </script>
@endsection
