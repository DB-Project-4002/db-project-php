<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use App\Traits\ModelViewableColumns;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    use ModelViewableColumns;

    /**
     * @var string[]
     */
    protected array $viewableColumns = [
        'match_participations.time',
        'match_participations.grade',
        'match_participations.kills',
        'match_participations.death',
        'match_participations.assists',
        'match_participations.cs',
        'match_participations.ds',
        'match_participations.dragons',
        'match_participations.rifts',
        'match_participations.item_1',
        'match_participations.item_2',
        'match_participations.item_3',
        'match_participations.item_4',
        'match_participations.item_5',
        'match_participations.item_6',
    ];



    /**
     * Get user match participations
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM match_participations
                                   WHERE match_participations.account_id = {$request->user_id}");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }



    /**
     * Get specific user match participations
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM match_participations
                                   WHERE match_participations.account_id = {$request->user_id}
                                   AND match_participations.time = {$request->match_time}");

            if (empty($data)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Match Time');
            }

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
