@extends('layouts.app')
@section('content')

  @include('flash') {{--  this showing flash meessage  --}}

  <div class="card" id="codeRefer">
        <h5 class="card-header text-center mb-4">Todolist</h5>


    <div class="card-body p-4" id="load">
        <div class="col-sm-12">
            @if (Session::has('message'))
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            @endif
            {{-- method="POST" action="{{route('todo.store')}}" --}}
            <form id="SubmitForm">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="project" name="project" placeholder="Project # To Do" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary mb-2" value="create">Create</button>
                    </div>
                </div>
            </form>

        <div class="form-check mb-3" id="appendCheckBox">
            <?php
            $Liststatus = '';
            if($status[0]->status == '1'){
                $Liststatus = 'checked';
            }
            ?>
            <input type="checkbox" id="statusValueId" value="{{ $status[0]->status }}" class="form-check-input" {{ $Liststatus }}>
            <label class="form-check-label" for="exampleCheck1">Show All Tasks</label>
          </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="getData">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Project Name</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="getData">
                    @foreach ($todos as $todo)
                    <?php
                    $status = '';
                    if($todo->completed == '1'){
                        $status = 'checked';
                    }
                    ?>
                    <tr>
                        <td><div class="form-check form-check-inline">
                            <input class="form-check-input" {{$status}} type="checkbox" onclick="get({{ $todo->id }},this.value)" id="updateStatus{{$todo->id}}" value="{{$todo->completed}}">
                          </div></td>
                        <td>@if($todo->completed)
                            {{$todo->project}} {{ $todo->created_at->diffForHumans() }}
                            @else
                            {{$todo->project}} {{ $todo->created_at->diffForHumans() }}
                        @endif</td>
                        <td>{{ $todo->created_at }}</td>
                        <td><a class="btn btn-default mb-2" onclick="deleteRecord({{$todo->id}})"><i class="fas fa-trash-alt"></i></a></td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            </div>
    </div>
  </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
@foreach ($todos as $todo)

<script>
//Update Project status
    $(document).on('change','#updateStatus{{ $todo->id }}',function (e) {
    const status = $('#updateStatus{{ $todo->id }}').val();
    let statusValue;
    if(status == 0){
        statusValue = 1;
    }else{
        statusValue = 0;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url: "{{route('todo.complete')}}",
      type:"POST",
      data:{
        completed:statusValue,
        id: {{ $todo->id }},
        tablename: 'todos',
      },
      success:function(response){
        $("#getData").load(" #getData > *");
        return alert("Task updated successfully");
      },
      });
});
</script>
@endforeach

<script type="text/javascript">

//Show Completed and incompleted task
$(document).on('change','#statusValueId',function (e) {
    const status = $('#statusValueId').val();
    if(status == 0){
        statusValue = 1;
    }else{
        statusValue = 0;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
      url: "{{route('todo.complete')}}",
      type:"POST",
      data:{
        status:statusValue,
        tablename: 'status',
      },
      success:function(response){
            $("#getData").load(" #getData > *");
            $("#appendCheckBox").load(" #appendCheckBox > *");
            if(status == 0){
                return alert("List of task completed");
            }else{
                return alert("List all task");
            }
        },
      });
});

//Add Project
$('#SubmitForm').on('submit',function(e){
    e.preventDefault();

    let project = $('#project').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
      url: "{{route('todo.store')}}",
      type:"POST",
      dataType: 'JSON',
      data:{
        project:project,
      },
      success:function(response){
        $('#SubmitForm').trigger("reset");
        $("#getData").load(" #getData > *");
        return alert("Record Added successfully");
      },
      });
    });

    //DELETE Project
    function deleteRecord(id){
        var url = '{{ route("todo.destroy", ":id") }}';
        url = url.replace(':id', id);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: url,
            type:"DELETE",
            beforeSend:function(){
                return confirm("Are you sure you want to delete it?");
            },
            success:function(response){
                $("#getData").load(" #getData > *");
                return alert("Record deleted successfully");
            },
        });
    }

    function get(id, val){
        const status = val;
        let statusValue;
    if(status == 0){
        statusValue = 1;
    }else{
        statusValue = 0;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
      url: "{{route('todo.complete')}}",
      type:"POST",
      data:{
        completed:statusValue,
        id: id ,
        tablename: 'todos',
      },
      success:function(response){
        $("#getData").load(" #getData > *");
        $("#updateStatus.id").load(" #updateStatus.id");
        return alert("Task updated successfully");
      },
      });
    }

  </script>
  @endsection

