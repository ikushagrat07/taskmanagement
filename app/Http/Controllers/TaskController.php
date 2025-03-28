<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function taskform()
    {
        return view('task');
    }

    public function addTask(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'task' => 'required|string|max:255',
        ], [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'max' => 'The :attribute cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors(),
            ]);
        }

        $taskDetails = [
            'task' => $request->post('task'),
        ];

        $existing_task = TaskModel::findTask($taskDetails);
        if ($existing_task) {
            return response()->json(['result' => 0, 'msg' => 'Duplicate task. Please enter a different task.']);
        }

        $task = TaskModel::storeTask($taskDetails);

        return response()->json(['result' => 1, 'msg' => 'Task has been added successfully.', 'task' => $task,]);
    }



    public function getAllTasks()
    {
        $tasks = TaskModel::getAllTasks();
        return response()->json(['result' => 1, 'tasks' => $tasks]);
    }


    public function deleteTask(Request $request)
    {
        $id = $request->post('id');
        $task = TaskModel::getTaskById($id);
        if (!$task) {
            return response()->json(['result' => 0, 'msg' => 'Task not found.']);
        }

        $result = TaskModel::deleteTask($id);
        if ($result) {
            return response()->json(['result' => 1, 'msg' => 'Task deleted successfully']);
        } else {
            return response()->json(['result' => -1, 'msg' => 'Try Again Later']);
        }
    }
    public function markAsComplete(Request $request)
    {

        $id = $request->post('id');
        // dd($id);

        $task = TaskModel::getTaskById($id);
        if (!$task) {
            return response()->json(['result' => 0, 'msg' => 'Task not found.']);
        }

        $result = TaskModel::markAsComplete($id);

        if ($result) {
            return response()->json(['result' => 1, 'msg' => 'Task marked as Completed successfully']);
        } else {
            return response()->json(['result' => -1, 'msg' => 'Try Again Later']);
        }
    }

}
