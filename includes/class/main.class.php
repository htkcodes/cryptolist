<?php

Class Main {
  private function request($url)
     {


         $curl = curl_init();

         curl_setopt_array($curl, array(
             CURLOPT_RETURNTRANSFER => 1,
             CURLOPT_URL => $url,
             CURLOPT_USERAGENT => 'Agent'
         ));

         return curl_exec($curl);

         curl_close($curl);

     }

     public function jsonCache($ctime)
     {
         global $request_type, $purge_cache, $limit_reached, $request_limit;

         $cache_file = dirname(__FILE__) . '/data/coins.json';
         $expires    = time() - $ctime;

         if (!file_exists($cache_file))
             die("Cache file is missing: $cache_file");

         if (filectime($cache_file) < $expires || file_get_contents($cache_file) == '' || $purge_cache && intval($_SESSION['views']) <= $request_limit) {
             $query = 'http://coincap.io/front';
             $api_results  = $this->request($query);
             $json_results = $api_results;

             if ($api_results && $json_results)
                 file_put_contents($cache_file, $json_results);

         } else {

             $json_results = file_get_contents($cache_file);
             $request_type = 'JSON';
         }

         return $json_results;
     }

}

?>
