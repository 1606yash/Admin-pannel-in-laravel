<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use App\Models\Folder;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends ApiBaseController
{
   /**
     * @OA\Get(
     *     path="/api/v1/documents",
     *     tags={"Document"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get documents",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getFoldersWithDocuments(Request $request)
    {
        try {
            $userId = FacadesAuth::user()->id;
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $folders = Folder::getFolders($userId, $perPage, $skip);
            if ($folders->isEmpty()) {
                return $this->sendSuccessResponse($folders, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($folders, 200, Config::get('constants.APIMESSAGES.FOLDER_AND_DOCUMENTS_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function downloadDocument($id)
    {
        try {
            $document = Document::findOrFail($id);
            if (Storage::disk('s3')->exists($document->path)) {
                $headers = [
                    'Content-Type' => 'application/jpeg',
                ];
            
                return \Response::make(Storage::disk('s3')->get($document->path), 200, $headers);
            } else {
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.FILE_NOT_FOUND'));
            }            
            } catch (\Exception $exception) {
                DB::rollback();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
