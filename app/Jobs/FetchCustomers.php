<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

use App\Models\MageCustomer;

class FetchCustomers extends BaseFetch
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function customHandle()
    {

        $filters = $this->getFilters();

        $response = $this->magento->api('customers')->all($this->pageSize, $this->currentPage, $filters);

        foreach ($this->getJsonItems($response) as $item) {

            $mageCustomer = MageCustomer::where('connection_id', $this->userConnection->id)->where('email', $item['email'])->first();

            if (!$mageCustomer) {
                $mageCustomer = new MageCustomer();
                $mageCustomer->connection_id = $this->userConnection->id;
                $mageCustomer->email = $item['email'];
            }
            try {
                $mageCustomer->json_id = $item['json_id'];
                $mageCustomer->save();
            } catch ( \Exception $ex) {
                Log::error($ex->getMessage());
            }
        }

        if ($this->currentPage === 0) {
            for ($i = 1; $i < $this->getNumberOfPagesInResponse(); $i++) {
                $this->batch()->add([
                    new FetchCustomers($this->userConnection, $i, $this->fetch)
                ]);
            }
        }
    }

}
