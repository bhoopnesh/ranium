<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class NeoStats extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->start!='' && $request->end!=''){
            $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=".$request->start ."&end_date=".$request->end."&api_key=8ITwudBvhdgbUTMo3ry0xa8SLKqWxvJA2Nn0riSQ";
            $client = new Client();
            $res = $client->get($url);
            echo $res->getStatusCode(); // 200
            echo $res->getBody();
        }else{

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->start!='' && $request->end!=''){
            $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=".$request->start ."&end_date=".$request->end."&api_key=8ITwudBvhdgbUTMo3ry0xa8SLKqWxvJA2Nn0riSQ";
            $client = new Client();
            $res = $client->get($url);

            $api_data = json_decode($res->getBody(), true);

            $data_by_dates = [];
            $data_by_array = [];

            $velocity_in_kmph = [];
            $distance_in_km = [];
            $diameter_in_km = [];
            $count_by_date = [];

            foreach ($api_data['near_earth_objects'] as $key => $value) {
                $data_by_dates[$key] = $value;
                foreach ($data_by_dates[$key] as $data_by_date) {
                    $data_by_array[] = $data_by_date;
                }
            }

            foreach ($data_by_array as $neo) {

                foreach ($neo['estimated_diameter'] as $estemetd_diameterkey => $value) {
                    if ($estemetd_diameterkey == 'kilometers') {
                        $diameter_in_km[] = ($value['estimated_diameter_min']+$value['estimated_diameter_max'])/2;
                    }
                }

                foreach ($neo['close_approach_data'] as $specification) {
                    foreach ($specification['relative_velocity'] as $relative_velocitykey => $value) {
                        if ($relative_velocitykey == 'kilometers_per_hour') {
                            $velocity_in_kmph[] = $value;
                        }
                    }
                    foreach ($specification['miss_distance'] as $miss_distancekey => $value) {
                        if ($miss_distancekey == 'kilometers') {
                            $distance_in_km[] = $value;
                        }
                    }
                }
            }

            //print_r($velocity_in_kmph);

            arsort($diameter_in_km);

            $avarageSizeAseroid = Arr::first($diameter_in_km);
            $avarageSizeAseroidkey = array_key_first($diameter_in_km);
            $avarageSizeAseroidId = $data_by_array[$avarageSizeAseroidkey]['id'];


            $data_by_date_arrkeys = array_keys($data_by_dates);

            foreach ($data_by_date_arrkeys as $key => $value) {
                $count_by_date[$value] = count($data_by_dates[$value]);
            }

            arsort($velocity_in_kmph);

            $fastestAseroid = Arr::first($velocity_in_kmph);
            $fastestAseroidkey = array_key_first($velocity_in_kmph);
            $fastestAseroidId = $data_by_array[$fastestAseroidkey]['id'];

            asort($distance_in_km);
            $closestAseroid = Arr::first($distance_in_km);
            $closestAseroidkey = array_key_first($velocity_in_kmph);
            $closestAseroidId = $data_by_array[$closestAseroidkey]['id'];

            $chart_count_by_date_arry_keys = array_keys($count_by_date);
            $chart_count_by_date_arry_values = array_values($count_by_date);

            $dataRet = array(
                'avarage'=> array('title' => 'Avarage Aseroid ','value'=>$avarageSizeAseroid,'id'=>$avarageSizeAseroidId),
                'fastest'=> array('title' => 'Fastest Aseroid ','value'=>$fastestAseroid,'id'=>$fastestAseroidId),
                'closest'=> array('title' => 'Closest Aseroid ','value'=>$closestAseroid,'id'=>$closestAseroidId),
                'chart' => array('value'=>$chart_count_by_date_arry_values,'id'=>$chart_count_by_date_arry_keys)
            );
            return response($dataRet, 200);
        }else{
            return response('Please select dates', 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
