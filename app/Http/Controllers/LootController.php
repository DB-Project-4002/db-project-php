<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $userId = (int)$request->user_id;
     
        $rawQuery = DB::raw("SELECT loots.name, loots.mythic_essence_price, loots.game_credit_price, loots.type, loot_ownerships.count
                                   FROM loots
                                   JOIN loot_ownerships
                                   ON loots.name = loot_ownerships.loot_name
                                   WHERE loot_ownerships.account_id = {$userId}");
        $data     = DB::select($rawQuery);

        return response()->json(['data' => $data]);
    }



    public function store(Request $request)
    {
        //DB::raw('SELECT count(*) FROM dbp')
    }

}
