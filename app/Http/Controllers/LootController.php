<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use App\Traits\ModelViewableColumns;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class LootController extends Controller
{
    use ModelViewableColumns;

    /**
     * @var array
     */
    protected array $viewableColumns = ['loot_ownerships.loot_name', 'loot_ownerships.count'];



    public function list(Request $request)
    {
        try {
            $data = DB::select("SELECT name, mythic_essence_price, game_credit_price, type FROM loots");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }


    /**
     * List of loots for current account
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $accountId = (int)$request->account_id;

            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM loots
                                   JOIN loot_ownerships
                                   ON loots.name = loot_ownerships.loot_name
                                   WHERE loot_ownerships.account_id = {$accountId}");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }



    /**
     * Store new loot or update the number of existing loots
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $lootName         = $request->loot_name;
            if (is_null($lootName)){
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Loot Name Required');
            }

            $accountId           = $request->account_id;
            $isLootNameExists = DB::selectOne("SELECT * FROM loots WHERE loots.name = '{$lootName}' ");
            if (is_null($isLootNameExists)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Loot Name');
            }

            $currentAccountLootNameExisting = DB::selectOne("SELECT * FROM loot_ownerships
                                                         WHERE loot_ownerships.account_id = {$accountId}
                                                         AND loot_ownerships.loot_name = '{$lootName}' ");

            if (is_null($currentAccountLootNameExisting)) {
                DB::insert("INSERT INTO loot_ownerships (account_id, loot_name, count) VALUES ($accountId, '$lootName', 1)");
            } else {
                DB::update("UPDATE loot_ownerships SET count = ({$currentAccountLootNameExisting->count} + 1) WHERE account_id = {$accountId} AND loot_name = '{$lootName}' ");
            }

            return $this->index($request);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
