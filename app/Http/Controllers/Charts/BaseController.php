<?php

namespace App\Http\Controllers\Charts;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\MageOrder;
use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{

    public $count_step = 30;

    /**
     * getPeriodString
     *
     * @param  string $period
     * @return string
     */
    protected function getPeriodString(string $period): string
    {
        switch ($period) {
            case 'today':
                $periodStr = '-1 day';
                break;
            case '7 days':
                $periodStr = '-7 day';
                break;
            case '30 days':
                $periodStr = '-30 day';
                break;
            default:
                $periodStr = '-3 months';
        }

        return $periodStr;
    }

    
    /**
     * getCurrencyCode
     *
     * @param  int $connection_id
     * @return string
     */
    public function getCurrencyCode($connection_id)
    {
        $currencyCode = Connection::find($connection_id);
        return $currencyCode ? $currencyCode['currency_code'] : '';
    }
    
    /**
     * addConditionPeriod
     *
     * @param  mixed $query
     * @param  string $field
     * @param  string $period
     * @param  mixed $start
     * @return mixed
     */
    public function addConditionPeriod(&$query, $field, $period, $start = false)
    {

        $end_ = date('Y-m-d', strtotime($start ? $start : now()));
        $start_ = date('Y-m-d', strtotime($end_ . ' ' . $this->getPeriodString($period)));

        $query->where($field, '>', $start_);
        $query->where($field, '<=', $end_);
        return $query;
    }
    
    /**
     * addGroup
     *
     * @param  mixed $query
     * @param  string $field
     * @param  string $period
     * @param  mixed $start
     * @param  mixed $id
     * @param  mixed $type
     * @return mixed
     */
    public function addGroup(&$query, $field, $period, $start = false, $id = 'id', $type = false)
    {

        $end_ = strtotime($start ? $start : now());
        $end = date('Y-m-d', strtotime($start ? $start : now()));
        $start_ = strtotime($end . ' ' . $this->getPeriodString($period));

        $step = round(($end_ - $start_) / $this->count_step);

        if (!$type) $query->select(DB::raw('ROUND(UNIX_TIMESTAMP(' . $field . ')/' . $step . ',0 ) as step , ' . $field . ' as step_data  , count(DISTINCT (' . $id . ')) as value'))
            ->groupBy('step');

        if ($type == 'avg') $query->select(DB::raw('ROUND(UNIX_TIMESTAMP(' . $field . ')/' . $step . ',0 ) as step , ' . $field . ' as step_data  , AVG(' . $id . ') as avg_value '))
            ->groupBy('step');

        if ($type == 'count') $query->select(DB::raw('ROUND(UNIX_TIMESTAMP(' . $field . ')/' . $step . ',0 ) as step , ' . $field . ' as step_data  , count(' . $id . ') as value_'))
            ->groupBy('step');

        if ($type == 'sum') $query->select(DB::raw('ROUND(UNIX_TIMESTAMP(' . $field . ')/' . $step . ',0 ) as step , ' . $field . ' as step_data  , sum(' . $id . ') as value'))
            ->groupBy('step');

        if ($type == 'all') $query->select(DB::raw('ROUND(UNIX_TIMESTAMP(' . $field . ')/' . $step . ',0 ) as step , ' . $field . ' as step_data  , count(DISTINCT (' . $id . ')) as value , count(' . $id . ') as value_ , AVG(' . $id . ') as avg_value '))
            ->groupBy('step');


        return $query;
    }
    
    /**
     * normalizeChartData
     *
     * @param  mixed $data
     * @param  string $period
     * @param  mixed $start
     * @param  mixed $value
     * @return array
     */
    public function normalizeChartData($data, $period, $start = false, $value = 'value')
    {

        $end_ = strtotime($start ? $start : now());
        $end = date('Y-m-d', strtotime($start ? $start : now()));
        $start_ = strtotime($end . ' ' . $this->getPeriodString($period));
        $step = round(($end_ - $start_) / $this->count_step);

        $nodata = true;
        $x = [];
        $y = [];
        for ($i = round($start_ / $step); $i <= round($end_ / $step); $i++) {
            $y[$i] = 0;
            $x[$i] =  date('Y-m-d H:i:s', $i * $step);
        }

        foreach ($data as $item) {
            if (is_object($item)) {
                $y[$item->step] = $item->$value;
                $nodata = false;
            } else {
                $y[$item['step']] = $item[$value];
                $nodata = false;
            }
        }

        return ['x' => array_values($x), 'y' => array_values($y), 'nodata' => $nodata];
    }

    public function getPercent($a, $b)
    {
        $pr = null;
        if ($b != 0) {
            $pr = round((($a / $b) * 100) - 100, 2);
        }
        return $pr;
    }
}
