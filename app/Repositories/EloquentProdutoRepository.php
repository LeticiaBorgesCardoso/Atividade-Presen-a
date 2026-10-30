<?php

namespace App\Repositories;

use App\Models\Produto;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProdutoRepository implements ProdutoRepositoryInterface
{
    public function search(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        return Produto::query()
                ->when($filtros['search'] ?? null,
                    fn ($query, $busca) => $query->where('nome', 'ILIKE',
                    "%{$busca}%")
                )
                // Filtro 1: faixa de preço (preco_min e/ou preco_max)
                ->when($filtros['preco_min'] ?? null,
                    fn ($query, $precoMin) => $query->where('preco', '>=', $precoMin)
                )
                ->when($filtros['preco_max'] ?? null,
                    fn ($query, $precoMax) => $query->where('preco', '<=', $precoMax)
                )
                // Filtro 2: somente produtos com estoque disponível
                ->when(filter_var($filtros['em_estoque'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    fn ($query) => $query->where('estoque', '>', 0)
                )
                ->paginate($porPagina);
    }

    public function create(array $dados): Produto
    {
        return Produto::create($dados);
    }

    public function update(Produto $produto, array $dados): Produto
    {
        $produto->update($dados);
        return $produto->refresh();
    }

    public function delete(Produto $produto): void
    {
        $produto->delete();
    }
}
