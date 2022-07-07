<?php

namespace App\Http\Controllers;

use App\Exceptions\WhiteHouse;
use App\Traits\ModelViewableColumns;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StickerController extends Controller
{
    use ModelViewableColumns;

    /**
     * @var array
     */
    protected array $viewableColumns = ['stickers.name as sticker_name'];


    public function list(Request $request)
    {
        try {
            $data = DB::select("SELECT name, game_credit_price FROM stickers");

            return WhiteHouse::generalResponse(Response::HTTP_OK, $data);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }


    /**
     * List of stickers for current account
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $data = DB::select("SELECT {$this->getViewableColumns()}
                                   FROM stickers
                                   JOIN sticker_ownerships
                                   ON stickers.name = sticker_ownerships.sticker_name
                                   WHERE sticker_ownerships.account_id = {$request->account_id}");

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
    public function store(Request $request): JsonResponse
    {
        try {
            $stickerName         = $request->sticker_name;
            if (is_null($stickerName)){
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Sticker Name Required');
            }

            $accountId           = $request->account_id;
            $isStickerNameExists = DB::selectOne("SELECT * FROM stickers WHERE stickers.name = '{$stickerName}' ");
            if (is_null($isStickerNameExists)) {
                return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Sticker Name');
            }

            $currentAccountStickerNameExisting = DB::selectOne("SELECT * FROM sticker_ownerships
                                                         WHERE sticker_ownerships.account_id = {$accountId}
                                                         AND sticker_ownerships.sticker_name = '{$stickerName}' ");

            if (is_null($currentAccountStickerNameExisting)) {
                DB::insert("INSERT INTO sticker_ownerships (account_id, sticker_name) VALUES ($accountId, '$stickerName')");
            } else {
              return  WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Sticker {$stickerName} already exists");
            }

            return $this->index($request);
        } catch (Exception $ex) {
            return WhiteHouse::generalResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WhiteHouse::SERVER_ERROR_MESSAGE);
        }
    }

}
