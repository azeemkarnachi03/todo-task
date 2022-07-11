<?php
namespace App\Http\Controllers;
use App\Http\Requests\TodoRequest;
use Illuminate\Http\Request;
use App\Models\Todo;
use DB;
use Session;


class TodoController extends Controller{

//     public function index(){
//         $todos = auth()->user()->todos()->orderBy('completed')->get();
//         return view('todos.index')->with(['todos' => $todos]);

//         // $todos = Todo::orderBy('completed')->get();
//         //  return view('todos.index', compact('todos'));
//    }


    public function create(){
        $status = DB::table('status')->get();
        if($status[0]->status == 1){
            $todos = auth()->user()->todos()->orderBy('created_at')->where('completed', 1)->get();
        }
        else{
            $todos = auth()->user()->todos()->orderBy('created_at')->get();
        }
        return view('todos.create',compact('status'))->with(['todos' => $todos]);
}


    public function store(TodoRequest $request){

        if($request->ajax()){
            if (DB::table('todos')->where('project', '=', $request->input('project'))->exists()){
                return'<script type="text/javascript">alert("hello!");</script>';
            } else {
                $data = auth()->user()->todos()->create($request->all());
                return response()->json([
                    'data' => $data
                ]);
            }}

    }


    public function edit($id){
       $todo = Todo::find($id);
       return view('todos.edit')->with(['todo' => $todo]);

    }

    public function update(TodoRequest $request,Todo $todo){
         $todo->update(['project' => $request->project]);
         return redirect(route('todo.index'))->with('message', 'Task Updated');
    }


    public function destroy(Todo $todo){
        $todo->delete();
        return response()->json([
            'data' => $todo
        ]);
      }

    public function complete(Request $request){
        // dd($request->completed);
        if($request->tablename == 'status'){
            $result = DB::table('status')->where('id', 1)->update(['status' => $request->status]);
        }else{
            $result = DB::table('todos')->where('id', $request->id)->update(['completed' => $request->completed]);
        }
        return response()->json([
            'data' => $result
        ]);
    }

    public function status(Request $request){
        $completed = $request->completed;
        $id = $request->id;
        $result = DB::table('todos') ->where('id', $id) ->update(['completed' => $completed]);
        return response()->json([
            'data' => $result
        ]);
    }

    public function incomplete(Todo $todo){
     $todo->update(['completed' => false]);
     return redirect()->back()->with('message', 'Task incomplete');
    }
}
