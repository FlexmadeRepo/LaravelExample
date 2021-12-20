<?php

namespace App\Http\Controllers\Charts;

use App\Models\MageCustomer;
use App\Models\MageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AverageOrderCustomerController extends BaseController
{
    /**
     * @param int $id
     * @param string $period
     * @return JsonResponse
     * @throws \Exception
     */
    public function index($id, $period , $start = false)
    {

        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $countOrders = MageOrder::where('connection_id', $id);
        $countOrders = $this->addConditionPeriod($countOrders,'mage_created_at',$period,$start)->count();

        $countCustomers = MageCustomer::where('connection_id', $id);
        $countCustomers = $this->addConditionPeriod($countCustomers,'created_at',$period,$start)->count();

        $result = 0;
        if ($countCustomers>0) $result = round($countOrders/$countCustomers,2);

        $this->count_step = 3;

        $query = MageOrder::where('connection_id', $id);
        $this->addConditionPeriod($query,'mage_created_at',$period,$start);
        $chart = $this->addGroup($query,"mage_created_at",$period,$start,'id','count')->get();
        $chartOrders = $this->normalizeChartData($chart,$period,$start,'value_');

        $query = MageCustomer::where('connection_id', $id);
        $this->addConditionPeriod($query,'created_at',$period,$start);
        $chart = $this->addGroup($query,"created_at",$period,$start,'id','count')->get();
        $chartCustomers = $this->normalizeChartData($chart,$period,$start,'value_');


        foreach ($chartCustomers['y'] as $k=>$v) {
            $r= 0;
            if ($v>0) $r = round(($chartOrders['y'][$k])/$v,2);
            $chartCustomers['y'][$k] = $r;
        }

        return response()->json(['result' => $result,'chart'=>$chartCustomers]);
    }
}
