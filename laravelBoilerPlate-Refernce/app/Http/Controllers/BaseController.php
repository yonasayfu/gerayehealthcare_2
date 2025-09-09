<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Default pagination size
     */
    protected int $perPage = 15;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct()
    {
        // Apply middleware in child classes as needed
    }

    /**
     * Get the current user
     *
     * @return \App\Models\User|null
     */
    protected function currentUser()
    {
        return Auth::user();
    }

    /**
     * Check if the current user has a specific role
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        $user = $this->currentUser();
        return $user && $user->hasRole($role);
    }

    /**
     * Check if the current user has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        $user = $this->currentUser();
        return $user && $user->can($permission);
    }

    /**
     * Return a success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, string $message = 'Operation successful', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error response
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = 'An error occurred', int $code = 400, array $errors = [])
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Flash a success message
     *
     * @param string $message
     * @return void
     */
    protected function flashSuccess(string $message): void
    {
        session()->flash('success', $message);
    }

    /**
     * Flash an error message
     *
     * @param string $message
     * @return void
     */
    protected function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    /**
     * Flash a warning message
     *
     * @param string $message
     * @return void
     */
    protected function flashWarning(string $message): void
    {
        session()->flash('warning', $message);
    }

    /**
     * Flash an info message
     *
     * @param string $message
     * @return void
     */
    protected function flashInfo(string $message): void
    {
        session()->flash('info', $message);
    }

    /**
     * Redirect with success message
     *
     * @param string $route
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectSuccess(string $route, string $message): \Illuminate\Http\RedirectResponse
    {
        $this->flashSuccess($message);
        return redirect()->route($route);
    }

    /**
     * Redirect with error message
     *
     * @param string $route
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectError(string $route, string $message): \Illuminate\Http\RedirectResponse
    {
        $this->flashError($message);
        return redirect()->route($route);
    }

    /**
     * Get pagination parameters from request
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'page' => $request->input('page', 1),
            'per_page' => $request->input('per_page', $this->perPage),
        ];
    }

    /**
     * Get sorting parameters from request
     *
     * @param \Illuminate\Http\Request $request
     * @param string $defaultColumn
     * @param string $defaultDirection
     * @return array
     */
    protected function getSortParams(Request $request, string $defaultColumn = 'id', string $defaultDirection = 'asc'): array
    {
        return [
            'sort' => $request->input('sort', $defaultColumn),
            'direction' => $request->input('direction', $defaultDirection),
        ];
    }

    /**
     * Get search parameters from request
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function getSearchParam(Request $request): ?string
    {
        return $request->input('search');
    }
}
