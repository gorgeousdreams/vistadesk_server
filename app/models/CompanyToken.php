<?php
class CompanyToken extends Eloquent {

    public static function createCompanyToken($companyId, $expireTime, $tokenType = null) {
        $companyToken = new CompanyToken;
        if ($expireTime) {
            $liveTime = new \DateTime();
            $liveTime->add($expireTime);
            $companyToken->expires_at = $liveTime->format('Y-m-d H:i:s');
        }
        $companyToken->token = generateUUID().generateUUID();
        $companyToken->token_type = $tokenType;
        $companyToken->company_id = $companyId;
        $companyToken->save();
        return $companyToken;
    }


    public function company() {
        return $this->belongsTo('Company');
    }
}