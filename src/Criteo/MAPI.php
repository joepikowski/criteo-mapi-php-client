<?php
/**
 * Criteo MAPI PHP Client
 * @version 1.0.0
 * @author Joe Pikowski <j.pikowski@criteo.com>
 */

 /**
  * Creates a new MAPI Client.
  */
    class Criteo_MAPI extends Criteo_API_Client {
        private $id;
        private $secret;
        private $token;

        public function __construct($id, $secret, $host = 'api.criteo.com', $endpoint = ''){
            $this->id = $id;
            $this->secret = $secret;
            parent::__construct($host, $endpoint);
        }

        /**
         * Get oauth2 token from id and secret provided on initialization.
         */
        public function authenticate(){
            $auth = [
                'client_id' => $this->id,
                'client_secret' => $this->secret,
                'grant_type' => 'client_credentials'
            ];
            $res = $this->post('/oauth2/token', $this->toFormData($auth));
            $json = json_decode($res['body'], true);
            $this->token = $json['access_token'];
            return $this->token;
        }

        /**
         * Get campaigns for a single advertiser.
         * @param integer|string $advertiser  Criteo advertiser ID.
         */
        public function getCampaignsByAdvertiser($advertiser){
            $res = $this->get("/v1/advertisers/$advertiser/campaigns");
            return json_decode($res['body'], true);
        }

        /**
         * Get all categories for all campaigns of a single advertiser.
         * @param integer|string $advertiser  Criteo advertiser ID.
         * @param boolean $enabled  Optional. Default false. Filter for enabled categories.
         */
        public function getCategoriesByAdvertiser($advertiser, $enabled = false){
            $data = [
                'enabledOnly' => $enabled
            ];
            $res = $this->get("/v1/advertisers/$advertiser/categories", $data);
            return json_decode($res['body'], true);
        }

        /**
         * Get category information for an advertiser, irrespective of specific campaigns.
         * @param integer|string $advertiser  Criteo advertiser ID.
         * @param integer|string $category  Category ID.
         */
        public function getCategoryByAdvertiser($advertiser, $category){
            $res = $this->get("/v1/advertisers/$advertiser/categories/$category");
            return json_decode($res['body'], true);
        }

        /**
         * Get the audiences for an advertiser.
         * @param integer|string $advertiser  Criteo advertiser ID.
         */
        public function getAudiences($advertiser){
            $data = [
                'advertiserId' => $advertiser
            ];
            $res = $this->get("/v1/audiences", $data);
            return json_decode($res['body'], true);
        }

        /**
         * Create an audience for an advertiser.
         * @param integer|string $advertiser  Criteo advertiser ID.
         * @param array $options {
         *      @param string name  Audience name.
         *      @param string description  Optional. Audience description.
         * }
         */
        public function createAudience($advertiser, $options = []){
            $data = [
                'advertiserId' => $advertiser,
                'name' => $options['name'],
                'description' => $options['description']
            ];
            $res = $this->post("/v1/audiences/userlist", json_encode($data));
            return json_decode($res['body'], true);
        }

        /**
         * Delete an audience by ID.
         * @param integer|string $audience  Audience ID.
         */
        public function deleteAudience($audience){
            $res = $this->delete("/v1/audiences/$audience");
            return $res['body'];
        }

        /**
         * Update the metadata of an audience.
         * @param integer|string $audience  Audience ID.
         * @param array $options {
         *      @param string name  Audience name.
         *      @param string description  Optional. Audience description.
         * }
         */
        public function updateAudience($audience, $options = []){
            $data = [
                'name' => $options['name'],
                'description' => $options['description']
            ];
            $res = $this->put("/v1/audiences/$audience", json_encode($data));
            return $res['body'];
        }

        /**
         * Remove all users from an audience.
         * @param integer|string $audience  Audience ID.
         */
        public function wipeAudience($audience){
            $res = $this->delete("/v1/audiences/userlist/$audience/users");
            return json_decode($res['body'], true);
        }

        /**
         * Add users to an audience.
         * @param integer|string $audience  Audience ID.
         * @param array $options {
         *      @param string schema  'email' or 'madid'
         *      @param string[] identifiers  An array of ids (limit 50000 per call).
         * }
         */
        public function addToAudience($audience, $options = []){
            $data = [
                'operation' => 'add',
                'schema' => $options['schema'],
                'identifiers' => $options['identifiers']
            ];
            $res = $this->patch("/v1/audiences/userlist/$audience", json_encode($data));
            return json_decode($res['body'], true);
        }

        /**
         * Remove users from an audience.
         * @param integer|string $audience  Audience ID.
         * @param array $options {
         *      @param string schema  'email' or 'madid'
         *      @param string[] identifiers  An array of ids (limit 50000 per call).
         * }
         */
        public function removeFromAudience($audience, $options = []){
            $data = [
                'operation' => 'remove',
                'schema' => $options['schema'],
                'identifiers' => $options['identifiers']
            ];
            $res = $this->patch("/v1/audiences/userlist/$audience", json_encode($data));
            return json_decode($res['body'], true);
        }

         /**
          * Get budgets for a list of advertisers or budget IDs.
          * @param array $options {
          *      @param integer|string advertiserIds  Optional.
          *      @param integer|string budgetIds  Optional.
          * }
          * @param boolean $active  Optional. Default true. Filter for budgets with active campaigns.
          */
        public function getBudgets($options = [], $active = true){
            $data = [
                'advertiserIds' => $options['advertiserIds'],
                'budgetIds' => $options['budgetIds'],
                'onlyActiveCampaigns' => $active
            ];
            $res = $this->get("/v1/budgets", $data);
            return json_decode($res['body'], true);
        }

         /**
          * Get campaigns by advertiser IDs or campaign IDs.
          * @param array $options {
          *      @param integer|string advertiserIds  Optional.
          *      @param integer|string campaignIds  Optional.
          *      @param string campaignStatus  Optional. Running, Archived or NotRunning
          *      @param boolean bidType  Optional. Unknown, CPC, COS, or CPO
          * }
          */
        public function getCampaigns($options = []){
            $res = $this->get("/v1/campaigns", $options);
            return json_decode($res['body'], true);
        }

        /**
         * Get campaign by ID.
         * @param integer|string $id
         */
        public function getCampaign($id){
            $res = $this->get("/v1/campaigns/$id");
            return json_decode($res['body'], true);
        }

        /**
         * Get categories by campaign ID.
         * @param integer|string $id
         * @param boolean $enabled  Optional. Default false. Filter for enabled categories.
         */
        public function getCategoriesByCampaign($id, $enabled = false){
            $data = [
                'enabledOnly' => $enabled
            ];
            $res = $this->get("/v1/campaigns/$id/categories", $data);
            return json_decode($res['body'], true);
        }

        /**
         * Get a specific campaign category.
         * @param integer|string $campaign  Campaign ID.
         * @param integer|string $category  Category ID.
         */
        public function getCategoryByCampaign($campaign, $category){
            $res = $this->get("/v1/campaigns/$campaign/categories/$category");
            return json_decode($res['body'], true);
        }

         /**
          * Get bids by advertisers, campaigns or categories.
          * @param array $options {
          *      @param integer|string advertiserIds  Optional.
          *      @param integer|string budgetIds  Optional.
          *      @param integer|string categoryHashCodes  Optional.
          *      @param string bidType  Optional. Unknown, CPC, COS, or CPO
          *      @param string campaignStatus  Optional. Running, Archived or NotRunning
          *      @param boolean pendingChanges  Optional. true or false
          * }
          */
        public function getBids($options = []){
            $res = $this->get("/v1/campaigns/bids", $options);
            return json_decode($res['body'], true);
        }

        /**
         * Update bids by campaign (campaign- and category-level).
         * @param array[] $campaigns {
         *      @param integer|string campaignId
         *      @param float|string bidValue
         *      @param array[] categories {
         *          Optional. An array of category objects, specifying bids that overwrite the overall campaign bid value.
         *
         *          @param integer|string categoryHashCode
         *          @param float|string bidValue
         *      }
         * }
         */
        public function updateBids($campaigns = []){
            $res = $this->put("/v1/campaigns/bids", json_encode($campaigns));
            return json_decode($res['body'], true);
        }

         /**
          * Get categories by campaigns, advertisers, or a list of categories.
          * @param array $options {
          *      @param integer|string campaignIds  Optional.
          *      @param integer|string advertiserIds  Optional.
          *      @param integer|string categoryHashCodes  Optional.
          * }
          * @param boolean $enabled  Optional. Default false. Filter for enabled categories.
          */
        public function getCategories($options = [], $enabled = false){
            $data = [
                'campaignIds' => $options['campaignIds'],
                'advertiserIds' => $options['advertiserIds'],
                'categoryHashCodes' => $options['categoryHashCodes'],
                'enabledOnly' => $enabled
            ];
            $res = $this->get("/v1/categories", $data);
            return json_decode($res['body'], true);
        }

        /**
         * Update categories by catalog.
         * @param array[] $catalogs {
         * @param integer|string catalogId
         * @param array[] categories {
         *      An array of category objects, specifying enabled or disabled.
         *
         *      @param integer|string categoryHashCode
         *      @param boolean enabled
         * }
         */
        public function updateCategories($catalogs = []){
            $res = $this->put("/v1/categories", json_encode($catalogs));
            return json_decode($res['body'], true);
        }

        /**
         * Get user's portfolio of advertiser accounts.
         */
        public function getPortfolio(){
            $res = $this->get("/v1/portfolio");
            return json_decode($res['body'], true);
        }

        /**
         * Get publisher-level data by advertisers.
         * @param array $options {
         *      @param string advertiserIds  Optional. Criteo advertiser IDs, comma-separated.
         *      @param string startDate  Starting date string, will be auto-converted to ISO for convenience.
         *      @param string endDate  Starting date string, will be auto-converted to ISO for convenience.
         * }
         */
        public function getPublisherStats($options = []){
            $data = [
                'advertiserIds' => $options['advertiserIds'],
                'startDate' => date('c',strtotime($options['startDate'])),
                'endDate' => date('c',strtotime($options['endDate']))
            ];
            $res = $this->post("/v1/publishers/stats", json_encode($data));
            return json_decode($res['body'], true);
        }

        /**
         * Get sellers by campaigns.
         * @param array $options {
         *      @param integer|string campaignIds  Optional. Criteo campaign IDs, comma-separated.
         *      @param boolean activeSellers  Optional. Default false. Filter for active sellers.
         *      @param boolean hasProducts  Optional. Default false. Filter for sellers with products in the catalog.
         *      @param boolean activeBudgets  Optional. Default false. Filter for sellers with active budgets.
         * }
         */
        public function getSellers($options = [], $activeSellers = false, $hasProducts = false, $activeBudgets = false){
            $data = [
                'campaignIds' => $options['campaignIds'],
                'onlyActiveSellers' => $activeSellers,
                'onlySellersWithProductsInCatalog' => $hasProducts,
                'onlyActiveBudgets' => $activeBudgets
            ];
            $res = $this->get("/v1/sellers", $data);
            return json_decode($res['body'], true);
        }

        /**
         * Update seller bids by campaign.
         * @param array[] $campaign {
         *      @param integer|string campaignId
         *      @param array[] sellerBids {
         *          An array of seller arrays, specifying bid values.
         *
         *          @param string sellerName
         *          @param float|string bid
         *      }
         * }
         */
        public function updateSellerBids($campaign){
            $res = $this->put("/v1/sellers/bids", json_encode($campaign));
            return json_decode($res['body'], true);
        }

        /**
         * Create seller budgets by campaign.
         * @param array[] $campaign {
         *      @param integer|string campaignId
         *      @param array[] sellerBudgets {
         *          An array of seller arrays, specifying budget details.
         *
         *          @param string sellerName
         *          @param float|string amount
         *          @param string startDate  Start date of the budget - Must be in ISO format.
         *          @param string endDate  End date of the budget - Must be in ISO format.
         *      }
         * }
         */
        public function createSellerBudgets($campaign){
            $res = $this->post("/v1/sellers/budgets", json_encode($campaign));
            return json_decode($res['body'], true);
        }

        /**
         * Create seller budgets by campaign.
         * @param array[] $campaign {
         *      @param integer|string campaignId
         *      @param array[] sellerBudgets {
         *          An array of seller arrays, specifying budget details.
         *
         *          @param integer|string budgetId
         *          @param float|string amount  Optional. Leave empty or set to null for uncapped budget.
         *          @param string startDate  Start date of the budget - Must be in ISO format.
         *          @param string endDate  End date of the budget - Must be in ISO format.
         *          @param string status  Optional. Active or Inactive
         *      }
         * }
         */
        public function updateSellerBudgets($campaign){
            $res = $this->put("/v1/sellers/budgets", json_encode($campaign));
            return json_decode($res['body'], true);
        }

        /**
         * Get sellers by campaigns.
         * @param array $options {
         *      @param integer|string campaignIds  Optional. Criteo campaign IDs, comma-separated.
         *      @param integer|string advertiserIds  Optional. Criteo campaign IDs, comma-separated.
         *      @param string status  Optional. Running, Archived or NotRunning.
         * }
         */
        public function getSellerCampaigns($options = [], $status = true){
            $data = [
                'campaignIds' => $options['campaignIds'],
                'advertiserIds' => $options['advertiserIds'],
                'status' => $status
            ];
            $res = $this->get("/v1/sellers/campaigns", $data);
            return json_decode($res['body'], true);
        }

        /**
         * Get reporting for sellers.
         * @param array $query {
         *      @param integer|string advertiserIds  List of advertiser IDs, comma-separated.
         *      @param string startDate  Start date of the report, will be auto-converted to ISO for convenience using PHP's strtotime().
         *      @param string endDate  End date of the report, will be auto-converted to ISO for convenience using PHP's strtotime().
         *      @param string[] dimensions  CampaignId, AdvertiserId, Seller, Day, Week, Month and/or Year
         *      @param string[] metrics  Clicks, AdvertiserCost and/or Displays
         *      @param string format  CSV, Excel, XML or JSON
         *      @param string currency  Optional. ISO Format, three letters.
         *      @param string timezone  Optional. GMT, PST or JST
         * }
         * @param string filepath  The directory path of a file to save the results to.
         */
        public function getSellerStats($query, $filepath = null){
            $query['startDate'] = date('c',strtotime($query['startDate']));
            $query['endDate'] = date('c',strtotime($query['endDate']));
            $res = $this->post("/v1/sellers/stats", json_encode($query));
            return $this->handleStatsResponse($res,$query,$filepath);
        }

        /**
         * Get reporting on campaign performance.
         * @param array $query {
         *      @param string reportType  CampaignPerformance, FacebookDPA or TransactionID
         *      @param boolean ignoreXDevice  Optional. Default false. Ignore cross-device data.
         *      @param integer|string advertiserIds  List of advertiser IDs, comma-separated.
         *      @param string startDate  Start date of the report, will be auto-converted to ISO for convenience using PHP's strtotime().
         *      @param string endDate  End date of the report, will be auto-converted to ISO for convenience using PHP's strtotime().
         *      @param string[] dimensions  CampaignId, AdvertiserId, Seller, Day, Week, Month and/or Year
         *      @param string[] metrics  Clicks, AdvertiserCost and/or Displays
         *      @param string format  CSV, Excel, XML or JSON
         *      @param string currency  Optional. ISO Format, three letters.
         *      @param string timezone  Optional. GMT, PST or JST
         * }
         * @param string filepath  The directory path of a file to save the results to.
         */
        public function getStats($query, $filepath = null){
            $query['startDate'] = date('c',strtotime($query['startDate']));
            $query['endDate'] = date('c',strtotime($query['endDate']));
            $res = $this->post("/v1/statistics", json_encode($query));
            return $this->handleStatsResponse($res,$query,$filepath);
        }

        public function handleStatsResponse($res, $query, $filepath){
            //Remove UTF-8 Byte Order Mark (BOM)
            if (0 === strpos(bin2hex($res['body']), 'efbbbf')) {
               $res['body'] = substr($res['body'], 3);
            }
            if ($filepath){
                $f = @fopen($filepath, 'w+');
                if (!$f){
                    throw new Exception("[Error Saving to File] Could not open $filepath .");
                }
                $r = fwrite($f, $res['body']);
                if (!$r){
                    throw new Exception("[Error Saving to File] Could not write to $filepath .");
                }
                return true;
            }else if (strtolower($query['format']) === 'json'){
                return json_decode($res['body'], true);
            }
            return $res['body'];
        }

        protected function toFormData($arr){
            $form_data = '';
            foreach ($arr as $k => $v){
                $form_data .= "{$k}={$v}&";
            }
            return rtrim($form_data,'&');
        }

        public function get($path, $data = []){
            return $this->request('GET', $path, $data);
        }

        public function post($path, $data = []){
            return $this->request('POST', $path, $data);
        }

        public function put($path, $data = []){
            return $this->request('PUT', $path, $data);
        }

        public function patch($path, $data = []){
            return $this->request('PATCH', $path, $data);
        }

        public function delete($path, $data = []){
            return $this->request('DELETE', $path, $data);
        }

        private function checkAuthentication($path, $retry){
            return ( ($this->token && !$retry) || $path === '/oauth2/token' );
        }

        public function request($method, $path, $data, $retry = false){
            if (!$this->checkAuthentication($path, $retry)){
                $this->authenticate();
            }
            try {
                return $this->_request($method, $path, $data);
            }catch(Exception $e){
                if ($e->getCode() === 401 && !$retry){
                    return $this->request($method, $path, $data, true);
                }else{
                    throw new Exception("[MAPI Request Failed] ".$e->getMessage(), $e->getCode());
                }
            }
        }

        private function _request($method, $path, $data){
            return $this->{"api{$method}"}([
                'protocol' => 'https',
                'path' => $path,
                'headers' => [
                    "Authorization: Bearer {$this->token}",
                    "Accept: application/json",
                    "Content-Type: application/json",
                    "User-Agent: criteo-php-client/1.0.0"
                ],
                'body' => $data
            ]);
        }
    }
?>
