<?php

class JsonStorage
{
    public static function read($file)
    {
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);

        if (empty($content)) {
            return [];
        }

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    public static function write($file, $data)
    {
        file_put_contents(
            $file,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    public static function add($file, $item)
    {
        $data = self::read($file);

        $data[] = $item;

        self::write($file, $data);

        return $item;
    }
}