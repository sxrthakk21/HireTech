<?php

class SimilarityService
{
    /*
     * Remove common English words.
     */
    private $stopWords = [

        'the',
        'a',
        'an',
        'and',
        'or',
        'of',
        'to',
        'in',
        'on',
        'for',
        'with',
        'is',
        'are',
        'was',
        'were',
        'be',
        'been',
        'this',
        'that',
        'as',
        'at',
        'by',
        'from',
        'will',
        'can',
        'should',
        'have',
        'has',
        'had',
        'our',
        'their',
        'they',
        'we',
        'you',
        'your',
        'and',
        'or'
    ];


    /*
     * Convert text into words.
     */
    public function tokenize($text)
    {
        $text = strtolower($text);

        $text = preg_replace(
            '/[^a-z0-9+#.]+/i',
            ' ',
            $text
        );

        $words = preg_split(
            '/\s+/',
            trim($text)
        );

        $result = [];

        foreach ($words as $word) {

            if ($word === '') {
                continue;
            }

            if (
                in_array(
                    $word,
                    $this->stopWords
                )
            ) {
                continue;
            }

            $result[] = $word;
        }

        return $result;
    }


    /*
     * Create word frequency vector.
     */
    public function termFrequency($text)
    {
        $words = $this->tokenize($text);

        $frequency = [];

        foreach ($words as $word) {

            if (!isset($frequency[$word])) {

                $frequency[$word] = 0;
            }

            $frequency[$word]++;
        }

        return $frequency;
    }


    /*
     * Calculate cosine similarity.
     */
    public function cosineSimilarity(
        $textA,
        $textB
    ) {

        $vectorA =
            $this->termFrequency($textA);

        $vectorB =
            $this->termFrequency($textB);


        if (
            empty($vectorA) ||
            empty($vectorB)
        ) {

            return 0;
        }


        $allWords = array_unique(
            array_merge(
                array_keys($vectorA),
                array_keys($vectorB)
            )
        );


        $dotProduct = 0;

        $magnitudeA = 0;

        $magnitudeB = 0;


        foreach ($allWords as $word) {

            $a =
                $vectorA[$word] ?? 0;

            $b =
                $vectorB[$word] ?? 0;


            $dotProduct +=
                $a * $b;

            $magnitudeA +=
                $a * $a;

            $magnitudeB +=
                $b * $b;
        }


        $magnitudeA =
            sqrt($magnitudeA);

        $magnitudeB =
            sqrt($magnitudeB);


        if (
            $magnitudeA == 0 ||
            $magnitudeB == 0
        ) {

            return 0;
        }


        $similarity =
            $dotProduct /
            (
                $magnitudeA *
                $magnitudeB
            );


        return round(
            $similarity * 100,
            2
        );
    }
}
