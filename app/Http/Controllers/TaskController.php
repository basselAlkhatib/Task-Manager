<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function addToFavorite($taskId)
    {
        Task::findOrFail($taskId);
        Auth::user()->favoriteTasks()->syncWithoutDetaching($taskId);
        return response()->json(['message' => 'Task added to favorites'], 200);
    }

        public function removeFromFavorite($taskId)
        {
            Auth::user()->favoriteTasks()->detach($taskId);
            return response()->json(['message' => 'Task removed from favorites'], 200);
        }
    public function getFavoriteTasks()
    {
        $favorites = Auth::user()->favoriteTasks()->get();
        return response()->json($favorites, 200);
    }






    public function getAllTasks()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }
    public function addCategoriesToTask(Request $request, $taskId)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        $task = Task::findOrFail($taskId);
        $task->categories()->attach($request->category_id);
        return response()->json('category attached sucessfuly', 200);
    }

    public function getTaskByPriority()
    {
        $tasks = Auth::user()
            ->tasks()
            ->orderByRaw("FIELD(priority,'high','medium','low')")
            ->get();

        return response()->json($tasks, 200);
    }


    public function getTaskCategories($taskId)
    {
        $categories = Task::findorfail($taskId)->categories;
        // $task->()attach(category_id);
        return response()->json($categories, '200');
    }


    public function index()
    {
        $tasks = Auth::User()->tasks;
        return response()->json($tasks, 200);
    }
    public function store(StoreTaskRequest $request)
    {
        $user_id = Auth::User()->id;
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;
        $task = Task::create($validatedData);
        return response()->json($task, 200);
    }
    public function update(UpdateTaskRequest $request, $id)
    {
        $user_id = Auth::User()->id;
        $task = Task::findOrFail($id);
        if ($task->user_id != $user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $task->update($request->validated());
        return response()->json($task, 200);
    }

    public function show(Request $request, $id)
    {
        $user_id = Auth::user()->id;

        $task = Task::findOrFail($id);
        if ($task->user_id != $user_id) {
            return response()->json(['message' => 'UnAuthorazied', 403]);
        }
        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $user_id = Auth::user()->id;
        $task = Task::findOrFail($id);
        if ($task->user_id != $user_id) {
            return response()->json(['message' => 'UnAuthraized', 403]);
        }
        $task->delete();
        return response()->json(null, 204);
    }

    public function getTaskUser($id)
    {

        $user = Task::find($id)->user;
        return response()->json($user, 200);
    }
}
