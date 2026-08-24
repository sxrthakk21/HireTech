<?php

require_once __DIR__ .
    '/SimilarityService.php';


class RankingService
{
    private $similarityService;


    public function __construct()
    {
        $this->similarityService =
            new SimilarityService();
    }


    /*
     * Calculate skill match.
     */
    public function calculateSkillScore(
        $jobSkills,
        $candidateSkills
    ) {

        if (empty($jobSkills)) {

            return [
                'score' => 0,
                'matched' => [],
                'missing' => []
            ];
        }


        $jobSkills =
            array_map(
                'strtolower',
                $jobSkills
            );


        $candidateSkills =
            array_map(
                'strtolower',
                $candidateSkills
            );


        $matched = [];

        $missing = [];


        foreach ($jobSkills as $skill) {

            if (
                in_array(
                    $skill,
                    $candidateSkills
                )
            ) {

                $matched[] =
                    $skill;
            } else {

                $missing[] =
                    $skill;
            }
        }


        $score =
            (
                count($matched) /
                count($jobSkills)
            ) * 100;


        return [

            'score' =>
            round($score, 2),

            'matched' =>
            $matched,

            'missing' =>
            $missing

        ];
    }


    /*
     * Detect keyword stuffing.
     */
    public function keywordStuffingPenalty(
        $jobSkills,
        $resumeText
    ) {

        $resumeLower =
            strtolower(
                $resumeText
            );


        if (empty($jobSkills)) {

            return 0;
        }


        $totalOccurrences = 0;


        foreach ($jobSkills as $skill) {

            $skillLower =
                strtolower($skill);


            $count =
                substr_count(
                    $resumeLower,
                    $skillLower
                );


            $totalOccurrences +=
                $count;
        }


        /*
         * Average number of times
         * required skills occur.
         */

        $average =
            $totalOccurrences /
            count($jobSkills);


        /*
         * Normal resumes usually don't
         * repeat the same skill excessively.
         */

        if ($average <= 2) {

            return 0;
        }


        if ($average <= 4) {

            return 5;
        }


        if ($average <= 7) {

            return 10;
        }


        if ($average <= 10) {

            return 15;
        }


        return 20;
    }


    /*
     * Experience score.
     */
    public function experienceScore(
        $resume
    ) {

        $experience =
            trim(
                $resume['experience'] ?? ''
            );


        if ($experience === '') {

            return 0;
        }


        $wordCount =
            str_word_count(
                $experience
            );


        if ($wordCount >= 100) {

            return 100;
        }


        if ($wordCount >= 50) {

            return 80;
        }


        if ($wordCount >= 20) {

            return 60;
        }


        return 40;
    }


    /*
     * Complete candidate score.
     */
    public function scoreCandidate(
        $job,
        $candidate
    ) {

        $jobDescription =
            $job['description'] ?? '';


        $resumeText =
            $candidate['resumeText'] ?? '';


        $jobSkills =
            $job['skills'] ?? [];


        $candidateSkills =
            $candidate['skills'] ?? [];


        /*
         * Semantic/text similarity
         */

        $semanticScore =
            $this->similarityService
            ->cosineSimilarity(
                $jobDescription,
                $resumeText
            );


        /*
         * Skill matching
         */

        $skillResult =
            $this->calculateSkillScore(
                $jobSkills,
                $candidateSkills
            );


        /*
         * Experience
         */

        $experienceScore =
            $this->experienceScore(
                $candidate
            );


        /*
         * Keyword stuffing
         */

        $keywordPenalty =
            $this->keywordStuffingPenalty(
                $jobSkills,
                $resumeText
            );


        /*
         * Final score
         *
         * Semantic      = 50%
         * Skills        = 30%
         * Experience    = 20%
         */

        $rawScore =

            ($semanticScore * 0.50)

            +

            ($skillResult['score'] * 0.30)

            +

            ($experienceScore * 0.20);


        $finalScore =
            $rawScore -
            $keywordPenalty;


        /*
         * Prevent negative score.
         */

        if ($finalScore < 0) {

            $finalScore = 0;
        }


        if ($finalScore > 100) {

            $finalScore = 100;
        }


        return [

            'semanticScore' =>
            round(
                $semanticScore,
                2
            ),

            'skillScore' =>
            round(
                $skillResult['score'],
                2
            ),

            'experienceScore' =>
            round(
                $experienceScore,
                2
            ),

            'keywordPenalty' =>
            $keywordPenalty,

            'matchedSkills' =>
            $skillResult['matched'],

            'missingSkills' =>
            $skillResult['missing'],

            'finalScore' =>
            round(
                $finalScore,
                2
            )

        ];
    }
}
