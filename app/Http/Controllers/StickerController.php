<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class StickerController extends Controller
{

    /**
     * List of stickers for current user
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $data = DB::select("SELECT stickers.name
                                   FROM stickers
                                   JOIN sticker_ownerships
                                   ON stickers.name = sticker_ownerships.sticker_name
                                   WHERE sticker_ownerships.account_id = {$request->user_id}");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }



    /**
     * Store new sticker
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $userId           = $request->user_id;
            $stickerName         = Route::current()->parameter('sticker_name');
            $isStickerNameExists = DB::selectOne("SELECT * FROM stickers WHERE stickers.name = '{$stickerName}' ");
            if (is_null($isStickerNameExists)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Sticker Name');
            }

            $currentUserStickerNameExisting = DB::selectOne("SELECT * FROM sticker_ownerships
                                                         WHERE sticker_ownerships.account_id = {$userId}
                                                         AND sticker_ownerships.sticker_name = '{$stickerName}' ");

            if (is_null($currentUserStickerNameExisting)) {
                DB::insert("INSERT INTO sticker_ownerships (account_id, sticker_name) VALUES ($userId, '$stickerName')");
            } else {
              return  WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Sticker {$stickerName} already exists");
            }

            return $this->index($request);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
