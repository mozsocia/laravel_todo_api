<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->authUser;
        $todos = Todo::where('user_id', $user->id)->get();

        return response()->json(['todos' => $todos], 200);
    }

    public function store(Request $request)
    {
        $user = $request->authUser;

        // Validate the request using Validator::make
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendJsonError('Validation error', $validator->errors(), 422);
        }

        try {
            $todo = new Todo([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
            ]);
            $todo->save();

            return response()->json(['message' => 'Todo created successfully.'], 201);
        } catch (\Exception $e) {
            return $this->sendJsonError('Error creating todo', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $request->authUser;

        // Validate the request using Validator::make
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'completed' => 'boolean', // Validation rule for 'completed' field
        ]);

        if ($validator->fails()) {
            return $this->sendJsonError('Validation error', $validator->errors(), 422);
        }

        try {
            $todo = Todo::where('user_id', $user->id)->findOrFail($id);

            $todo->update([
                'title' => $request->title,
                'description' => $request->description,
                'completed' => $request->completed, // Update the 'completed' field
            ]);

            return response()->json(['message' => 'Todo updated successfully.'], 200);
        } catch (\Exception $e) {
            return $this->sendJsonError('Error updating todo', ['error' => $e->getMessage()], 500);
        }
    }
    public function destroy(Request $request, $id)
    {
        $user = $request->authUser;

        try {
            $todo = Todo::where('user_id', $user->id)->findOrFail($id);

            $todo->delete();

            return response()->json(['message' => 'Todo deleted successfully.'], 200);
        } catch (\Exception $e) {
            return $this->sendJsonError('Error deleting todo', ['error' => $e->getMessage()], 500);
        }
    }

    // Helper method to send JSON error responses
    private function sendJsonError($message, $errors, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
