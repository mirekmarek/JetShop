<?php

namespace Twisto;


class SharedDbCustomer
{
    public string $email;
    public string $facebook_id;

    public function __construct(string $email, string $facebook_id)
    {
        $this->email = $email;
        $this->facebook_id = $facebook_id;
    }

    public function serialize() : array
    {
        $data = [
            'email_hash' => md5(trim(strtolower($this->email))),
        ];

        if ($this->facebook_id) {
	        $data['facebook_id_hash'] = md5($this->facebook_id);
        }

        return $data;
    }
}