<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TemplateFormRequest;
use App\Models\Template;
use App\Services\TemplateService;

class TemplateController extends Controller
{
    public function __construct(private TemplateService $templateService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->templateService->paginate($request);
    }

    public function all()
    {
        return response()->json(Template::with(['image:id,path,fileable_id'])->get(), 200);
    }

    public function store(TemplateFormRequest $request)
    {

        return $this->templateService->store($request->all());
    }

    public function update(TemplateFormRequest $request, Template $template)
    {
        return $this->templateService->update($template, $request->all());
    }

    public function delete(Template $template)
    {
        return $this->templateService->delete($template);
    }
}
