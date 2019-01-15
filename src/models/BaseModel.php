<?php

namespace mywishlist\models;

use Illuminate\Database\Capsule\Manager as DBManager;

abstract class BaseModel extends \Illuminate\Database\Eloquent\Model{

    public function delete() : bool
	{
        $connection = DBManager::connection();
		$connection->beginTransaction();
		$val = $this->doDelete();

        if ($val)
            $val = parent::delete();

		if ($val)
        {
			$connection->commit();
        }
		else
		{
            $connection->rollback();
        }

        if ($val)
            $val = $this->postDelete();

		return $val;
	}

    //Fait la vraie suppression du modele, que de la bdd
	protected abstract function doDelete() : bool;

    //Apres la suppression reussie
    protected function postDelete() : bool
    {
        return true;
    }

}
