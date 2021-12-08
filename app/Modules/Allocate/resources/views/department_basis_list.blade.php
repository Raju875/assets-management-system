<div class="tab-pane" id="department_basis">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <label class="col-md-3">Select Department</label>
                        <select name="dept_id" class="form-control col-md-8" id="dept_id">
                            <option value="all" selected>All</option>
                            @foreach($departments as $key=>$dept)
                                <option value="{{ $key }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table id="department_basis_list" class="table table-bordered table-striped nowrap"
           cellspacing="0"
           width="100%">
        <thead>
        <tr>
            <th>SL</th>
            <th>Asset(Code)</th>
            <th>Category</th>
            <th>Sub category</th>
            <th>Assign to</th>
            <th>Updated by</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
