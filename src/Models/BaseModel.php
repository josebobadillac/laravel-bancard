<?php

namespace Mancoide\Bancard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BaseModel extends Model
{
    /**
     * @return string
     */
    public function getConnectionName()
    {
        $databaseConnection = config('bancard.db_connection');
        if (!empty($databaseConnection)) {
            $this->connection = $databaseConnection;
        }
        return parent::getConnectionName();
    }
}
