<?php

namespace pompong\Api\V1\Controllers;

// vendor
use Tymon\JWTAuth\Exceptions\JWTException;

// transformers
use pompong\Api\V1\Transformers\GenreTransformer;

// models
use pompong\Models\Genre;

class GenreController extends BaseController
{
    public function index()
    {
        try {
            return $this->collection(Genre::all(), new GenreTransformer);
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not get genres'], 500);
        }
    }
}
