<?php
namespace Reducer;

/**
 * Account Reducer
 * 
 * @param $snapshot contains payload and action 
 * @example [ 'action' => 'AddNewAccount', 'payload' => [] ]
 */

 class AccountReducer {
    public $payload;

    function __construct() {
        $this->payload = [
            'action'  => 'None',
            'payload' => []
        ];
    }

    public function reduce($snapshot) {

        switch($snapshot['action']) {
            case 'ADD':
                $this->payload = array_merge($snapshot['payload'], $this->payload);
                break;
            case 'NEW':
                $this->payload = $snapshot['payload'];
                break;
            case 'DELETE':
                $this->payload = array_diff($this->payload, $snapshot['payload']);
        }

        return $this->payload;
    }

    public function add_service() {
        
    }
 }