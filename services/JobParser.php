<?php

class JobParser
{
    private $skills = [
        'php',
        'laravel',
        'mysql',
        'javascript',
        'bootstrap',
        'html',
        'css',
        'react',
        'angular',
        'vue',
        'python',
        'java',
        'c++',
        'node.js',
        'nodejs',
        'mongodb',
        'rest api',
        'api',
        'git',
        'github',
        'docker',
        'flutter',
        'dart',
        'machine learning',
        'deep learning',
        'tensorflow',
        'pytorch'
    ];

    public function extractSkills($text)
    {
        $text = strtolower($text);

        $found = [];

        foreach ($this->skills as $skill) {

            if (strpos($text, strtolower($skill)) !== false) {
                $found[] = $skill;
            }
        }

        return array_values(array_unique($found));
    }

    public function analyze($text)
    {
        return [
            'requiredSkills' => $this->extractSkills($text),
            'wordCount' => str_word_count($text)
        ];
    }
}
