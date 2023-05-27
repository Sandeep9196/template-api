<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateDetailRequest;
use App\Models\TemplateDetail;
use App\Services\TemplateDetailService;
use Illuminate\Http\Request;

class TemplateDetailController extends Controller
{

    public function __construct(private TemplateDetailService $templateDetailService)
    {
    }

    public function get()
    {
        return $this->templateDetailService->get();
    }
    public function getData()
    {
        return $this->templateDetailService->getData();
    }


    public function store(TemplateDetailRequest $request)
    {

        return $this->templateDetailService->store($request->all());
    }

    public function update(TemplateDetailRequest $request, TemplateDetail $templateDetail)
    {

        return $this->templateDetailService->update($templateDetail, $request->all());
    }

    public function delete(TemplateDetail $templateDetail)
    {
        return $this->templateDetailService->delete($templateDetail);
    }

}
