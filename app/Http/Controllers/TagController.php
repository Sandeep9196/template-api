<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagFormRequest;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(private TagService $tagService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->tagService->paginate($request);
    }

    public function all()
    {
        return response()->json(Tag::all(), 200);
    }

    public function store(TagFormRequest $request)
    {

        return $this->tagService->store($request->all());
    }

    public function update(TagFormRequest $request, Tag $tag)
    {
        return $this->tagService->update($tag, $request->all());
    }

    public function delete(Tag $tag)
    {
        return $this->tagService->delete($tag);
    }

    public function deleteImage(Request $request)
    {
        return $this->tagService->deleteImage($request->id);
    }
}
