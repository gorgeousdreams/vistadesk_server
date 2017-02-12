<?php
class EncryptedModel extends AppModel {

    protected $encrypt = [];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encrypt) && !is_null($value))
        {
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->encrypt) && !is_null($this->attributes[$key]))
        {
            return Crypt::decrypt($this->attributes[$key]);
        }

        return parent::getAttribute($key);
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($attributes as $key => $value)
        {
            if (in_array($key, $this->encrypt) && !is_null($value))
            {
                $attributes[$key] = Crypt::decrypt($value);
            }
        }

        return $attributes;
    }

}