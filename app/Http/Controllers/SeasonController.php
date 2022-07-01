<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class SeasonController extends Controller
{

    public function index(Request $request)
    {
        try {
            $data = DB::select("SELECT seasons.number, seasons.rating, seasons.ranking
                                   FROM seasons
                                   WHERE seasons.account_id = {$request->user_id}");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }


    public function show(Request $request)
    {
        try {
            $data = DB::select("SELECT seasons.number, seasons.rating, seasons.ranking
                                   FROM seasons
                                   WHERE seasons.account_id = {$request->user_id}
                                   AND seasons.number = {$request->season_num}");

            if (empty($data)){
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Season number');
            }

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
