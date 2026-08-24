<?php

class ResumeParser
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
        'pytorch',
        'sql',
        'jquery',
        'ajax',
        'typescript',
        'wordpress',
        'linux',
        'aws'
    ];


    /**
     * Extract text from resume
     */
    public function extractText($filePath, $extension)
    {
        $extension = strtolower($extension);

        if ($extension === 'docx') {
            return $this->extractDocxText($filePath);
        }

        if ($extension === 'pdf') {
            return $this->extractPdfText($filePath);
        }

        return '';
    }


    /**
     * Extract DOCX text
     */
    private function extractDocxText($filePath)
    {
        if (!class_exists('ZipArchive')) {

            return '';
        }

        $zip = new ZipArchive();

        if ($zip->open($filePath) !== true) {

            return '';
        }

        $xml = $zip->getFromName(
            'word/document.xml'
        );

        $zip->close();

        if ($xml === false) {

            return '';
        }

        $xml = str_replace(
            '</w:p>',
            "\n",
            $xml
        );

        $xml = str_replace(
            '</w:tr>',
            "\n",
            $xml
        );

        $text = strip_tags($xml);

        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        );

        return $this->cleanText($text);
    }


    /**
     * Extract PDF text
     */
    private function extractPdfText($filePath)
    {
        $content = @file_get_contents(
            $filePath
        );

        if ($content === false) {

            return '';
        }

        /*
         * Extract text from common PDF
         * text objects.
         */

        $text = '';

        preg_match_all(
            '/BT(.*?)ET/s',
            $content,
            $blocks
        );

        if (!empty($blocks[1])) {

            foreach ($blocks[1] as $block) {

                preg_match_all(
                    '/\((.*?)\)\s*Tj/s',
                    $block,
                    $matches
                );

                if (!empty($matches[1])) {

                    foreach ($matches[1] as $match) {

                        $text .=
                            $match . ' ';
                    }
                }
            }
        }

        /*
         * Handle hexadecimal PDF strings.
         */

        preg_match_all(
            '/<([0-9A-Fa-f]+)>\s*Tj/',
            $content,
            $hexMatches
        );

        if (!empty($hexMatches[1])) {

            foreach ($hexMatches[1] as $hex) {

                $decoded = '';

                for (
                    $i = 0;
                    $i < strlen($hex);
                    $i += 2
                ) {

                    $decoded .= chr(
                        hexdec(
                            substr(
                                $hex,
                                $i,
                                2
                            )
                        )
                    );
                }

                $text .=
                    $decoded . ' ';
            }
        }

        return $this->cleanText($text);
    }


    /**
     * Clean extracted text
     */
    private function cleanText($text)
    {
        $text = preg_replace(
            '/\s+/',
            ' ',
            $text
        );

        $text = preg_replace(
            '/[^\P{C}\n]+/u',
            ' ',
            $text
        );

        return trim($text);
    }


    /**
     * Extract skills
     */
    public function extractSkills($text)
    {
        $textLower = strtolower($text);

        $foundSkills = [];

        foreach ($this->skills as $skill) {

            if (
                strpos(
                    $textLower,
                    strtolower($skill)
                ) !== false
            ) {

                $foundSkills[] = $skill;
            }
        }

        return array_values(
            array_unique($foundSkills)
        );
    }


    /**
     * Extract section
     */
    public function extractSection(
        $text,
        $sectionNames
    ) {

        $pattern =
            implode(
                '|',
                array_map(
                    function ($section) {

                        return preg_quote(
                            $section,
                            '/'
                        );
                    },
                    $sectionNames
                )
            );


        $regex =
            '/(?:' .
            $pattern .
            ')\s*:?\s*(.*?)(?=\b(?:experience|education|skills|projects|summary|objective|certifications|achievements|personal details)\b|$)/is';


        if (
            preg_match(
                $regex,
                $text,
                $matches
            )
        ) {

            return trim(
                $matches[1]
            );
        }


        return '';
    }


    /**
     * Analyze resume
     */
    public function analyze($text)
    {
        return [

            'skills' =>
            $this->extractSkills(
                $text
            ),

            'experience' =>
            $this->extractSection(
                $text,
                [
                    'experience',
                    'work experience',
                    'professional experience'
                ]
            ),

            'education' =>
            $this->extractSection(
                $text,
                [
                    'education',
                    'academic qualification',
                    'qualifications'
                ]
            ),

            'wordCount' =>
            str_word_count(
                $text
            ),

            'text' => $text

        ];
    }
}
