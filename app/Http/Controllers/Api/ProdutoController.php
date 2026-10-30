<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    
    public function __construct(private ProdutoService $service)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtros = [
            'search' => $request->query('search'),
            'preco_min' => $request->query('preco_min'),
            'preco_max' => $request->query('preco_max'),
            'em_estoque' => $request->query('em_estoque'),
        ];

        return ProdutoResource::collection($this->service->listar($filtros));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProdutoRequest $request)
    {
        $dados = $request->validated();

        $produto =  $this->service->create($dados);

        // Código 201 : Recurso Criado
        return new ProdutoResource($produto);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return new ProdutoResource($produto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProdutoRequest $request, Produto $produto)
    {
        $dados = $request->validated();

        $produto = $this->service->update($produto, $dados);

        return new ProdutoResource($produto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        $this->service->delete($produto);
        return response()->noContent(); // 204
    }

}
