<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Fetch all tasks for the authenticated user
    public function index()
    {
        return response()->json(Task::where('user_id', Auth::id())->get());
    }

    // Store a new task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in-progress,completed',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'status' => $request->status,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['task'=>$task,'message'=>'Task Added Successfully'], 201);
    }

    // Update a task
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:pending,in-progress,completed',
        ]);

        $task->update($request->only(['title', 'status']));

        return response()->json($task);
    }

    // Delete a task
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
