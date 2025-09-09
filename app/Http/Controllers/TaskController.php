<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Exception;
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
            'category_id' => 'required|array',
            'category_id.*' => 'integer|exists:categories,id',
        ]);

        $task = Task::findOrFail($taskId);

        // Avoid duplicates
        $task->categories()->syncWithoutDetaching($request->category_id);

        return response()->json(['message' => 'Categories attached successfully'], 200);
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

        try {
            $task = Task::findOrFail($id);

            // Check if the authenticated user owns the task
            if ($task->user_id != $user_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $task->delete();
            return response()->json(['message' => 'Task Deleted Successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task Not Found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }


    public function getTaskUser($id)
    {

        $user = Task::find($id)->user;
        return response()->json($user, 200);
    }
}
