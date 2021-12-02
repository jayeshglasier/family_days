<table id="example3" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">User Id</th>
                 <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Profile Image: activate to sort column descending" aria-sort="ascending">Profile Image</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending">First Name</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Last Name: activate to sort column ascending">Last Name</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($users as $data)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $data->Use_Use_Id }}</td>
                    <td><img src={{asset('public/images/profile_images').'/'.$data->Use_Pro_Image}} style="height: 50px; width: 50px;" /></td>
                  <td>{{ $data->Use_Email_Id }}</td>
                  <td>{{ $data->Use_First_Name }}</td>
                  <td>{{ $data->Use_Last_Name }}</td>
                  <td>
                    <form class="row" method="POST" action="{{ route('user-management.destroy', ['id' => $data->Use_Id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('user-management.edit',['id' => $data->Use_Id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                        Update
                        </a>
                         <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                          Delete
                        </button>
                    </form>
                  </td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="5%" rowspan="1" colspan="1">User Id</th>
                <th  width="10%" rowspan="1" colspan="1">Profile Image</th>
                <th  width="20%" rowspan="1" colspan="1">Email</th>
                <th  width="20%" rowspan="1" colspan="1">First Name</th>
                <th  width="20%" rowspan="1" colspan="1">Last Name</th>
                <th rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
