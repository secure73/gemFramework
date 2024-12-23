<?php
namespace Gemvc\Traits\Model;
use Gemvc\Http\Request;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;
trait ListTrait
{

    public function list(Request $request):JsonResponse
    {
        // Handle pagination
        if (isset($request->get['page'])) {
            $this->setPage((int)$request->get['page']);
        }

        // Handle ordering
        if (isset($request->get['orderby'])) {
            $orderParts = explode(',', $request->get['orderby']);
            foreach ($orderParts as $order) {
                $parts = explode(':', $order);
                $column = $parts[0];
                $direction = $parts[1] ?? null;
                $this->orderBy($column, $direction === 'asc');
            }
        }

        // Handle search/filter
        if (isset($request->get['search'])) {
            foreach ($request->get['search'] as $column => $value) {
                $this->whereLike($column, $value);
            }
        }

        // Handle between filters
        if (isset($request->get['between'])) {
            foreach ($request->get['between'] as $column => $range) {
                $values = explode(',', $range);
                if (count($values) === 2) {
                    $this->whereBetween($column, $values[0], $values[1]);
                }
            }
        }

        // Handle exact matches
        if (isset($request->get['where'])) {
            foreach ($request->get['where'] as $column => $value) {
                if ($value === 'null') {
                    $this->whereNull($column);
                } elseif ($value === 'not_null') {
                    $this->whereNotNull($column);
                } else {
                    $this->where($column, $value);
                }
            }
        }

        // Execute query and return response
        $jsonResponse = new JsonResponse();
        
        if (!$this->getError()) {
            $result = $this->select()->limit($_ENV['QUERY_LIMIT'])->run();
           return Response::success($result, $this->getTotalCount());
        } 
        return Response::badRequest($this->getError());
    }
}
