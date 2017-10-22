<?php

namespace Components\Traits;

/**
 * Class SoftDeleteTrait.
 *
 * Make the active record have the soft delete behavior.
 */
trait SoftDeleteTrait
{
    public $deletedStatus = 0;

    public function softDelete()
    {
        /* @var $this \yii\db\ActiveRecord */
        $this->status = $this->deletedStatus;

        return $this->update(false, ['status']) !== false;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->status == $this->deletedStatus;
    }
}
