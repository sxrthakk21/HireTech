<?php

class IdGenerator
{
    public static function job()
    {
        return 'JOB-' . strtoupper(substr(uniqid(), -6));
    }

    public static function candidate()
    {
        return 'CAN-' . strtoupper(substr(uniqid(), -6));
    }
}
