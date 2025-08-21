<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\JsonResponse;

class TodoController extends Controller
{
    // CRUD Endpoints for Todolist
    public function index()
    {
        return auth()->user()->todos()->with('items.user')->get();
    }

    public function show(Todo $todo): JsonResponse
    {
        $todo->load('items.user');

        return response()->json($todo);
    }

    public function store(Request $request): JsonResponse
    {
        $todo = auth()->user()->todos()->create($request->validate(['title' => 'required|string']));
        return response()->json($todo, 201);
    }

    // Endpoint to invite a user by username
    public function invite(Request $request, Todo $todo): JsonResponse
    {
        Gate::authorize('manage-todolist', $todo);

        $request->validate(['username' => 'required|string']);
        $userToInvite = User::where('username', $request->username)->firstOrFail();
        $todo->members()->attach($userToInvite->id);

        return response()->json(['message' => 'User invited successfully.']);
    }

    // Endpoint for adding an item
    public function addItem(Request $request, Todo $todo): JsonResponse
    {
        if (!$todo->members()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'You are not a member of this todo list.'], 403);
        }

        $item = $todo->items()->create([
            'name' => $request->name,
            'user_id' => auth()->id()
        ]);

        event(new \App\Events\TodoItemCreated($item));

        return response()->json($item, 201);
    }
}
