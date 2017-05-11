<?php

namespace pompong\Api\V1\Controllers;

// transformers
use pompong\Api\V1\Transformers\ShowTransformer;

// models
use pompong\Models\Show;

class ShowController extends BaseController
{
    public function index()
    {
        try {
            $shows = Show::orderby('status', 'asc')
                ->orderby('show_name', 'asc')
                ->get();

            return $this->collection($shows, new ShowTransformer);
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not get genres'], 500);
        }
    }
}
