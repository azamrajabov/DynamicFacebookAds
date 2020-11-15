<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 12/06/2018
 * Time: 09:54
 */

namespace DynamicFbAds\Models;

abstract class ModelAbstract
{
    private $id;

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
}