<?php

namespace Modules\User\Http\Controllers;

use App\Models\Folder as ModelsFolder;
use App\Models\Document as ModelsDocument;
use App\Models\FolderPermissionCategory as ModelsFolderPermissionCategory;
use App\Models\FolderRolePermission as ModelsFolderRolePermission;
use App\Models\Role as ModelsRole;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Helpers;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $folders = ModelsFolder::get();
        return view('user::documents/index', compact('folders'));
    }

    public function createFolder(Request $request)
    {
        try {

            $data = [];
            $data['name'] = $request->folder_name;
            $data['created_by'] = Auth::user()->id ?? null;
            $checkFolderName = ModelsFolder::where('name', $request->folder_name)->first();
            if ($checkFolderName) {
                return response()->json(['status' => 'Fail', 'message' => trans('messages.FOLDER_ALREADY_EXIST')]);
            }
            DB::beginTransaction();
            $createFolder = ModelsFolder::create($data);
            if ($createFolder) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.FOLDER_CREATED')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function viewDocuments(Request $request)
    {
        $folderId = $request->id;
        $folder = ModelsFolder::where('id', $folderId)->first();
        $folderName = $folder->name;
        $getDocuments = ModelsDocument::where('folder_id', $folderId)->get();
        return view('user::documents/view_documents', compact('folderName', 'getDocuments', 'folderId'));
    }

    public function renameFolder(Request $request)
    {
        try {
            $folderId = $request->rename_folder_id;
            $folderName = $request->rename_folder;

            $checkFolderName = ModelsFolder::where('name', $request->rename_folder)->first();

            if ($checkFolderName && $checkFolderName->id != $folderId) {
                return response()->json(['status' => 'Fail', 'message' => trans('messages.FOLDER_ALREADY_EXIST')]);
            }

            DB::beginTransaction();
            $folder = ModelsFolder::where('id', $folderId)->first();
            $updateFolder = $folder->update(['name' => $folderName]);
            if ($updateFolder) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.FOLDER_RENAMED')]);
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function deleteFolder(Request $request)
    {
        try {
            $folderId = $request->id;
            $folder = ModelsFolder::where('id', $folderId)->first();
            if (empty($folder)) {
                return response()->json(['status' => 'Fail', 'message' => trans('messages.FOLDER_NOT_EXIST')]);
            }
            DB::beginTransaction();
            $deleteFolder = $folder->delete();
            if ($deleteFolder) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.FOLDER_DELETED')]);
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function uploadDocumentInFolder(Request $request)
    {
        try {
            DB::beginTransaction();
            $folderId = $request->folderId;
            if ($request->hasFile('attach_file')) {
                $folderName = 'Documents';
                foreach ($request->file('attach_file') as $file) {
                    $attachmentUrl = Helpers::uploadAttachment($file, $folderName, $folderId);

                    // Create a new attachment record in the database
                    $attachment = new ModelsDocument([
                        'name' => $file->getClientOriginalName(),
                        'path' => $attachmentUrl,
                        'folder_id' => $folderId,
                        'created_by' => Auth::user()->id ?? null
                    ]);

                    // Associate the attachment with the task
                    $uploadDocument = $attachment->save();
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => trans('messages.DOCUMENT_UPLOADED_SUCCESS')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function deleteDocuments(Request $request)
    {
        try {
            $documentId = $request->id;
            $document = ModelsDocument::where('id', $documentId)->first();
            if (empty($document)) {
                return response()->json(['status' => 'Fail', 'message' => trans('messages.DOCUMENT_NOT_FOUND')]);
            }
            DB::beginTransaction();
            $deleteDocument = $document->delete();
            if ($deleteDocument) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.DOCUMENT_DELETED_SUCCESS')]);
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function viewFolderPermissions(Request $request)
    {
        $folderId = $request->id;
        $roles = ModelsRole::getAllRoles();
        $folder = ModelsFolder::where('id', $folderId)->first();
        $folderPermissionCategories = ModelsFolderPermissionCategory::get();
        return view('user::documents/manage_permissions', compact('folder', 'folderPermissionCategories', 'roles'));
    }

    public function getFolderPermissionByRoleId(Request $request)
    {
        $folderId = $request->folder_id;
        $roleId = $request->role_id;
        $permissions = ModelsFolderRolePermission::select('folder_permission_categories.category_name', 'folder_role_permissions.id', 'folder_role_permissions.role_id', 'folder_role_permissions.folder_id', 'folder_role_permissions.permission_category_id')
            ->leftJoin('folder_permission_categories', 'folder_role_permissions.permission_category_id', '=', 'folder_permission_categories.id')
            ->where('folder_role_permissions.role_id', $roleId)
            ->where('folder_role_permissions.folder_id', $folderId)
            ->get();

        return response()->json(['folderPermissions' => $permissions]);
    }

    public function updateFolderPermissions(Request $request)
    {
        try {
            //dd($request->all());
            DB::beginTransaction();
            $permissionData = json_decode($request->categoryData, true);
            $folderId = $request->folder_id;
            $roleId = $request->role_id;

            $getPermissions = ModelsFolderRolePermission::select()
                ->where('folder_role_permissions.role_id', $roleId)
                ->where('folder_role_permissions.folder_id', $folderId)
                ->get();

            // if existing permiossion found delete them
            if (!empty($getPermissions)) {
                $deletePermissions = ModelsFolderRolePermission::select()
                    ->where('folder_role_permissions.role_id', $roleId)
                    ->where('folder_role_permissions.folder_id', $folderId)
                    ->delete();
            }
            // Iterate through permission data and assign permissions
            if (!empty($permissionData)) {
                foreach ($permissionData as $permission) {
                    if ($permission['categoryName'] === 'masterToggle') {
                        continue; // Skip to the next iteration
                    }
                    // Assign full access to all permissions in the category
                    if ($permission['categoryPermission'] == 'full-access') {
                        $data = [];
                        $data['role_id'] = $roleId;
                        $data['folder_id'] = $folderId;
                        $data['permission_category_id'] = $permission['categoryId'];
                        $data['created_by'] = 1;
                        $data['updated_by'] = 1;
                        $createFolderPermissions = ModelsFolderRolePermission::create($data);
                    } else {
                        DB::rollback();
                        // Return failure response if permission IDs are not found
                        return response()->json(['status' => 'Fail', 'message' => 'hi']);
                    }
                }
                if ($createFolderPermissions) {
                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.FOLDER_PERMISSION_UPDATED_SUCCESS')]);
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'Fail', 'message' => 'hello']);
                }
            }
            DB::Commit();
            return response()->json(['status' => 'success', 'message' => trans('messages.FOLDER_PERMISSION_UPDATED_SUCCESS')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }
}
