<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskModel extends Model
{
    use HasFactory;

    public static function storeTask($taskDetails)
    {
        $taskId = DB::table('task_management')->insertGetId($taskDetails);
        return DB::table('task_management')->where('id', $taskId)->first();
    }
    public static function getAllTasks()
    {
        return DB::table('task_management')
        ->where('status', '=', 'Active')
        ->get();
    }

    public static function getTaskById($id)
    {
        return DB::table('task_management')->where('id', $id)->where('status', '=', 'Active')->first();
    }
    public static function deleteTask($id)
    {
        return DB::table('task_management')
            ->where('id', $id)
            ->update(['status' => 'Deleted']);
    }

    public static function markAsComplete($id)
    {
        return DB::table('task_management')
            ->where('id', $id)
            ->where('status', '=', 'Active')
            ->update(['completed' => 'true']);

    }

    public static function findTask($taskDetails)
    {
       return  DB::table('task_management')
       ->where('task', $taskDetails['task'])
       ->where('status', '=', 'Active')
        ->first();
    }
}
