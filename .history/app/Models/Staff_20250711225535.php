// app/Http/Controllers/Admin/StaffController.php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff; // Assuming you have a Staff model
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id'); // Default sort field
        $direction = $request->input('direction', 'asc'); // Default sort direction
        $perPage = $request->input('per_page', 10); // Default per page

        $query = Staff::query();

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('position', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting (basic example, adjust to your actual columns)
        $validSortColumns = ['id', 'first_name', 'last_name', 'email', 'phone', 'position', 'department'];
        if (!in_array($sort, $validSortColumns)) {
            $sort = 'id'; // Default to a safe column
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }
        // For full_name, you might need a custom sort or a raw expression
        if ($sort === 'full_name') {
             $query->orderByRaw("CONCAT(first_name, ' ', last_name) " . $direction);
        } else {
             $query->orderBy($sort, $direction);
        }


        $staffMembers = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staffMembers, // <--- THIS IS THE CRUCIAL PROP
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => (int)$perPage,
            ],
        ]);
    }
}