<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use App\Traits\ModelViewableColumns;
use Carbon\Carbon;
use Exception;
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
        'match_participations.account_id',
        'accounts.name',
        'accounts.tag',
        'match_participations.team',
        'match_participations.champion_name',
        'match_participations.time',
        'match_participations.grade',
        'match_participations.kills',
        'match_participations.deaths',
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



    public function list(Request $request)
    {
        try {
            if (is_null($request->time)){
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid parameter time');
            }

            $matchDateTime = Carbon::createFromTimestamp($request->time)->toDateTimeString();
            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM match_participations
                                   JOIN accounts ON accounts.id = match_participations.account_id
                                   WHERE match_participations.time = '{$matchDateTime}'");

            foreach ($data as $matchData){
                $matchData->time = Carbon::parse($matchData->time)->timestamp;
            }

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }


    /**
     * Get account match participations
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
                                   JOIN accounts ON accounts.id = match_participations.account_id
                                   WHERE match_participations.account_id = {$request->account_id}");

            foreach ($data as $matchData){
                $matchData->time = (int)Carbon::parse($matchData->time)->timestamp;
            }

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }



    /**
     * Get specific account match participations
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $matchDateTime = Carbon::createFromTimestamp($request->match_time)->toDateTimeString();

            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM match_participations
                                   JOIN accounts ON accounts.id = match_participations.account_id
                                   WHERE match_participations.account_id = {$request->account_id}
                                   AND match_participations.time = '{$matchDateTime}'");

            if (empty($data)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Match Time');
            }

            $data[0]->time = $request->match_time;

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
