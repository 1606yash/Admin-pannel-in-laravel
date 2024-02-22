<?php

namespace Modules\User\Transformers;

use App\Http\Transformers\FractalPresenter;

class UserPresenter extends FractalPresenter
{

    /**
     * @return ProductTransformer
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}
