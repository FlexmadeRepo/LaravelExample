<?php

namespace App\Http\Controllers\Charts;

use App\Models\MageOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class Top10SoldProductsController extends BaseController
{
    /**
     * @param int $id
     * @param string $period
     * @param mixed $start
     * @param mixed $start1
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function index($id, $period, $start = false, $start1 = false): JsonResponse
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $soldProducts = MageOrderItem::where('connection_id', $id)
            ->select('sku')
            ->selectRaw('sum(qty_ordered) sold_products')
            ->groupBy('sku')
            ->orderByDesc('sold_products')
            ->limit(10);
        $soldProducts = $this->addConditionPeriod($soldProducts, 'created_at', $period, $start)->get();

        $sku = [];
        foreach ($soldProducts as $item) {
            $sku[] = $item['sku'];
        }

        $soldProducts_ = MageOrderItem::where('connection_id', $id)
            ->select('sku')
            ->selectRaw('sum(qty_ordered) sold_products')
            ->whereIn('sku', $sku)
            ->groupBy('sku');
        $soldProducts_ = $this->addConditionPeriod($soldProducts_, 'created_at', $period, $start1)->get();

        foreach ($soldProducts as $item) {
            $item['pr'] = null;
            foreach ($soldProducts_ as $item_) {
                $item['sold_products'] =$item['sold_products']*1;
                if ($item['sku'] == $item_['sku']) {
                    $item['pr'] = $this->getPercent($item['sold_products'], $item_['sold_products']);

                    break;
                }
            }
        }

        return response()->json(['soldProducts' => $soldProducts]);
    }
}
