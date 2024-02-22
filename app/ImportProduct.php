<?php
namespace App;
 
use Config;
use Modules\Ecommerce\Entities\ImportResult;
use Modules\Ecommerce\Exports\ProductErrorExport;

class ImportProduct
{
	public static function save_result($result_id)
	{
		$checkResult = ImportResult::findOrfail($result_id);
        $checkResult->rows_imported = 858585;
        $checkResult->save();

        if(!empty($checkResult->errors)){
            $errors = json_decode($checkResult->errors,true);
            $fileName = 'products_errors-'.$result_id.'.csv';
            $file = '/uploads/products/imports/' . $fileName;
        	$storeExcel = (new ProductErrorExport($errors))->store($file, 'uploads');

        	// if (file_exists(public_path($file))) {
         //        unlink(public_path($file));
         //    }
        }
	}
}