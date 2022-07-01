<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use App\Traits\ModelViewableColumns;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    use ModelViewableColumns;

    /**
     * @var string[]
     */
    protected array $viewableColumns = [
        'seasons.number',
        'seasons.rating',
        'seasons.ranking',
    ];



    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM seasons
                                   WHERE seasons.account_id = {$request->user_id}");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }



    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM seasons
                                   WHERE seasons.account_id = {$request->user_id}
                                   AND seasons.number = {$request->season_num}");

            if (empty($data)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Season Number');
            }

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }
}
