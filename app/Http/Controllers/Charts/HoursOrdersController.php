<?php

namespace App\Http\Controllers\Charts;

// use App\Http\Controllers\Controller;
use App\Models\MageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoursOrdersController extends BaseController
{

    /**
     * index
     *
     * @param  int $id
     * @param  string $period
     * @param  mixed $start
     * @param  mixed $start1
     * @return JsonResponse
     */
    public function index($id, $period, $start = false, $start1 = false)
    {
        $periodStr = $this->getPeriodString($period);

        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");


        $value = MageOrder::where('connection_id', $id)
            ->select(DB::raw('DATE_FORMAT(mage_created_at,"%H") as hour, sum(total_paid) as total ,count(id) as orders'))
            ->groupBy('hour')
            ->orderBY('hour');


        $value = $this->addConditionPeriod($value, 'mage_created_at', $period, $start)->get();


        $value_ = MageOrder::where('connection_id', $id)
            ->select(DB::raw('DATE_FORMAT(mage_created_at,"%H") as hour, sum(total_paid) as total ,count(id) as orders'))
            ->groupBy('hour')
            ->orderBY('hour');

        $value_ = $this->addConditionPeriod($value_, 'mage_created_at', $period, $start1)->get();

        foreach ($value as $item) {
            $item['pr'] = null;
            $item['pr_orders'] = null;
            foreach ($value_ as $item_) {
                if ($item['hour'] == $item_['hour']) {
                    $item['hour'] = $item['hour'] . '-' . str_pad($item['hour'] + 1 < 24 ? $item['hour'] + 1 : 0, 2, '0', STR_PAD_LEFT);
                    $item['total_'] = $item_['total'];
                    $item['orders_'] = $item_['orders'];
                    $item['pr'] = $this->getPercent($item['total'], $item_['total']);
                    $item['pr_orders'] = $this->getPercent($item['orders'], $item_['orders']);
                    break;
                }
            }
        }

        return response()->json(['result' => $value, 'currencyCode' => $this->getCurrencyCode($id)]);
    }
}
