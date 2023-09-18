<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\CrosswordRepository;
use Illuminate\Http\Request;

class CrosswordController extends Controller
{
    public function __construct(private CrosswordRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (is_null($request->get('date'))) {
            return response()->json([
                'code' => 400,
                'error' => 'Date field not specified'
            ], 400);
        }

        return response()->json($this->repository->list($request->get('date')));
    }
}
