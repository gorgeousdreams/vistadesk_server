<?php

class MimicTokenSeeder extends Seeder {

    public function run()
    {
        Eloquent::unguard();
        
        UserToken::create([
            'token'      => generateUUID(), 
            'token_type' => 'mimic',
            'user_id' => 1
        ]);
        
        UserToken::create([
            'token'      => generateUUID(), 
            'token_type' => 'mimic',
            'user_id' => 2
        ]);
        
        UserToken::create([
            'token'      => generateUUID(), 
            'token_type' => 'mimic',
            'user_id' => 3
        ]);
        
        UserToken::create([
            'token'      => generateUUID(), 
            'token_type' => 'mimic',
            'user_id' => 4
        ]);

    }
}