<?php

namespace App\Http\Controllers\Charts;

use App\Models\MageOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NewCustomersController extends BaseController
{
    /**
     * @param int $id
     * @param string $period
     * @return JsonResponse
     * @throws \Exception
     */
    public function index($id, $period, $start = false)
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $newCustomersCount = MageOrder::where('connection_id', $id)
            ->select(DB::raw('id , count(`customer_id`) as customer_count'))
            ->groupBy('customer_id')
            ->having('customer_count', '=', 1);

        $newCustomersCount = $this->addConditionPeriod($newCustomersCount, 'mage_created_at', $period, $start)->count();

        $returningCustomersCount = MageOrder::where('connection_id', $id)
            ->select(DB::raw('id , count(`customer_id`) as customer_count'))
            ->groupBy('customer_id')
            ->having('customer_count', '>', 1);

        $returningCustomersCount = $this->addConditionPeriod($returningCustomersCount, 'mage_created_at', $period, $start)->count();

        $query = MageOrder::where('connection_id', $id);
        $this->addConditionPeriod($query, 'mage_created_at', $period, $start);
        $chart = $this->addGroup($query, 'mage_created_at', $period, $start, 'customer_id', 'count')
            ->groupBy('customer_id')
            ->having('value_', '=', 1)->get();


        $query = MageOrder::where('connection_id', $id);
        $this->addConditionPeriod($query, 'mage_created_at', $period, $start);
        $chart1 = $this->addGroup($query, 'mage_created_at', $period, $start, 'customer_id', 'count')
            ->groupBy('customer_id')
            ->having('value_', '>', 1)->get();

        return response()->json([
            'newCustomersCount' => $newCustomersCount,
            'returningCustomersCount' => $returningCustomersCount,
            'chart' => $this->normalizeChartData($chart, $period, $start, 'value_'),
            'chart1' => $this->normalizeChartData($chart1, $period, $start, 'value_'),
        ]);
    }
}
