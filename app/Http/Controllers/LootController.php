<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class LootController extends Controller
{

    /**
     * List of loots for current user
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = (int)$request->user_id;

            $data = DB::select("SELECT loots.name, loot_ownerships.count
                                   FROM loots
                                   JOIN loot_ownerships
                                   ON loots.name = loot_ownerships.loot_name
                                   WHERE loot_ownerships.account_id = {$userId}");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }



    public function store(Request $request)
    {
        try {
            $userId           = $request->user_id;
            $lootName         = Route::current()->parameter('loot_name');
            $isLootNameExists = DB::selectOne("SELECT * FROM loots WHERE loots.name = '{$lootName}' ");
            if (is_null($isLootNameExists)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Loot Name');
            }

            $currentLootNameExisting = DB::selectOne("SELECT * FROM loot_ownerships
                                                         WHERE loot_ownerships.account_id = {$userId}
                                                         AND loot_ownerships.loot_name = '{$lootName}' ");

            if (is_null($currentLootNameExisting)) {
                DB::insert("INSERT INTO loot_ownerships (account_id, loot_name, count) VALUES ($userId, '$lootName', 1)");
            } else {
                DB::update("UPDATE loot_ownerships SET count = ({$currentLootNameExisting->count} + 1) WHERE account_id = {$userId} AND loot_name = '{$lootName}' ");
            }

            return $this->index($request);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
