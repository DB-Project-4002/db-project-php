<?php

namespace App\Traits;

trait ModelViewableColumns
{

    /**
     * Viewable columns for model
     *
     * @return string
     */
    protected function getViewableColumns(): string
    {
        return implode(',', $this->viewableColumns);
    }
}
