<?php

namespace App\Jobs;

use App\Models\JsonItems;
use App\Services\Fetch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;

use App\Models\Connection;
use Grayloon\Magento\Magento;

abstract class BaseFetch implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $magento;
    protected $userConnection;

    protected $pageSize = 200;
    protected $currentPage;

    protected $apiTotalCount;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 360;
    public $fetch;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->userConnection->id;
    }

    /**
    * Calculate the number of seconds to wait before retrying the job.
    *
    * @return array
    */
    public function backoff()
    {
        return [20, 300, 300];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Connection $connection, $currentPage = 0 , Fetch $fetch)
    {
        //
        $this->userConnection = $connection;
        $this->currentPage = $currentPage;
        $this->fetch = $fetch;

        $this->magento = new Magento(
            $baseUrl = $this->userConnection->host,
            $token = $this->userConnection->access_token,
            $version = 'V1',
            $basePath = 'rest',
            $storeCode = $this->userConnection->connection_id??'all'
        );

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }
        
        $this->customHandle();
    }

    abstract protected function customHandle();

    protected function getFilters()
    {
        $searchCriteria = [
            'searchCriteria[filterGroups][0][filters][0][field]' => 'created_at',
            'searchCriteria[filterGroups][0][filters][0][value]' => date('Y-m-d', strtotime('-3 months')),
            'searchCriteria[filterGroups][0][filters][0][conditionType]' => 'gt',

            'searchCriteria[sort_orders][0][field]' => 'created_at',
            'searchCriteria[sort_orders][0][direction]' => 'DESC',
        ];

        return $searchCriteria;
    }

    protected function getJsonItems($response)
    {
        $json = $response->json();

        if (empty($json)) {
            throw new \Exception("Error Processing Request (".get_class($this).")", 1);
        }

        $items = [];
        if (!empty($json['items'])) {
            $items = $json['items'];

            foreach ($items as &$i) {
                $j = new JsonItems();
                $j->json = json_encode($i);
                $j->save();
                $i['json_id'] = $j->id;
            }
        }

        if (!empty($json['total_count'])) {
            $this->apiTotalCount = $json['total_count'];
        }

        return $items;
    }

    protected function isLastPage()
    {
        return !($this->currentPage*$this->pageSize<=$this->apiTotalCount)||empty($this->apiTotalCount);
    }

    protected function getNumberOfPagesInResponse()
    {
        $numberOfPagesInResponse = 0;
        if ($this->apiTotalCount) {
            $numberOfPagesInResponse = ceil($this->apiTotalCount/$this->pageSize);
        }

        return $numberOfPagesInResponse;
    }

    public function failed( $exception )
    {
        $this->fetch->log( $this->userConnection->id , $this->batch()->id,'Error',get_class($this).' '.$exception->getMessage());
    }
}
