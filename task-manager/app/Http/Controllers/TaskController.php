<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Task;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;

class TaskController extends Controller
{
    

    // index メソッドを追加する
    public function index($id){

        $folders = Folder::all();
        $folder = Folder::find($id);
        // $tasks = Task::where('folder_id', '=', $folder->id)->get();
        $tasks = $folder->tasks()->get();

        // echo $tasks[0]->id;

        return view('tasks/index', [
            'folders' => $folders,
            "folder_id" => $id,
            'tasks' => $tasks
        ]);
    }

    /**
     *  【タスク作成ページの表示機能】
     *  
     *  GET /folders/{id}/tasks/create
     *  @param int $id
     *  @return \Illuminate\View\View
     */
    public function showCreateForm(int $id){

        $folder = Auth::user()->folders()->findOrFail($id);

        return view('tasks/create', [
            'folder_id' => $folder->id
        ]);
    }

    /**
     *  【タスクの作成機能】
     *
     *  POST /folders/{id}/tasks/create
     *  @param int $id
     *  @param CreateTask $request
     *  @return \Illuminate\Http\RedirectResponse
     */
    public function create(int $id, CreateTask $request){

        $folder = Auth::user()->folders()->FindOrFail($id);

        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;
        $folder->tasks()->save($task);
        // $task->save();

        return redirect()->route('tasks.index', [
            'id' => $folder->id,
        ]);
    }

    /**
     *  【タスク編集ページの表示機能】
     *  機能：タスクIDをフォルダ編集ページに渡して表示する
     *  
     *  GET /folders/{id}/tasks/{task_id}/edit
     *  @param int $id
     *  @param int $task_id
     *  @return \Illuminate\View\View
     */
    public function showEditForm(int $id, int $task_id){

        $folder = Auth::user()->folders()->findOrFail($id);
        // $task = $folder->find($task_id);
        $task = $folder->tasks()->find($task_id);

        return view('tasks/edit', [
            'task' => $task,
        ]);
    }

    /**
     *  【タスクの編集機能】
     *  機能：タスクが編集されたらDBを更新処理をしてタスク一覧にリダイレクトする
     *  
     *  POST /folders/{id}/tasks/{task_id}/edit
     *  @param int $id
     *  @param int $task_id
     *  @param EditTask $request
     *  @return \Illuminate\Http\RedirectResponse
     */
    public function edit(int $id, int $task_id, EditTask $request){

        // $task = Task::find($task_id);
        $folder = Auth::user()->folders()->findOrFail($id);
        $task = $folder->tasks()->findOrFail($task_id);

        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save();

        return redirect()->route('tasks.index', [
            'id' => $task->folder_id,
        ]);
    }

    /**
     *  【タスク削除ページの表示機能】
     *
     *  GET /folders/{id}/tasks/{task_id}/delete
     *  @param int $id
     *  @param int $task_id
     *  @return \Illuminate\View\View
     */
    public function showDeleteForm(int $id, int $task_id){

        $folder = Auth::user()->folders()->findOrFail($id);
        $task = $folder->tasks()->find($task_id);

        return view('tasks/delete', [
            'task' => $task,
        ]);
    }

    /**
     *  【タスクの削除機能】
     *
     *  POST /folders/{id}/tasks/{task_id}/delete
     *  @param int $id
     *  @param int $task_id
     *  @return \Illuminate\View\View
     */
    public function delete(int $id, int $task_id){

        $folder = Auth::user()->folders()->findOrFail($id);
        $task = $folder->tasks()->find($task_id)->delete();

        return redirect()->route('tasks.index', [
            'id' => $folder->id,
        ]);
    }
}
