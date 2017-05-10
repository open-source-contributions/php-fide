<?php

namespace Lindelius\FIDE;

/**
 * Class RatingSystem
 *
 * An implementation of the FIDE Rating System ({@link https://www.fide.com/fide/handbook.html?id=172&view=article}).
 *
 * @author  Tom Lindelius <tom.lindelius@gmail.com>
 * @version 2017-05-10
 */
class RatingSystem
{
    /**
     * @var int
     */
    const WON = 1;

    /**
     * @var int
     */
    const DRAW = 0;

    /**
     * @var int
     */
    const LOST = -1;

    /**
     * Calculates the rating change for a given contestant versus a given
     * opponent with a given outcome.
     *
     * @param Contestant $contestantA
     * @param Contestant $contestantB
     * @param int        $outcome
     * @return int
     */
    public static function calculateRatingChange(Contestant $contestant, Contestant $opponent, $outcome)
    {
        $higherRated      = $contestant->currentRating() >= $opponent->currentRating();
        $ratingDifference = static::getRatingDifference($contestant, $opponent);
        $scoreProbability = static::getScoreProbability($ratingDifference, $higherRated);
        
        $k = 20;

        if ($contestant->totalMatchesPlayed() < 30) {
            $k = 40;
        } elseif ($contestant->highestRating() >= 2400) {
            $k = 10;
        }

        $score = 0.5;

        if ($outcome === static::WON) {
            $score = 1;
        } elseif ($outcome === static::LOST) {
            $score = 0;
        }

        return (int) round(($score - $scoreProbability) * $k);
    }

    /**
     * Gets the absolute rating difference between two contestants.
     *
     * @param Contestant $contestant
     * @param Contestant $opponent
     * @return int
     */
    protected static function getRatingDifference(Contestant $contestant, Contestant $opponent)
    {
        $absoluteDifference = abs($contestant->currentRating() - $opponent->currentRating());

        return min($absoluteDifference, 400);
    }

    /**
     * Gets the score probability for a given rating difference.
     *
     * @param int  $ratingDifference
     * @param bool $higherRated
     * @return float
     */
    protected static function getScoreProbability($ratingDifference, $higherRated = true)
    {
        $ratingDifferences = [
            0 => 50,
            4 => 51,
            11 => 52,
            18 => 53,
            26 => 54,
            33 => 55,
            40 => 56,
            47 => 57,
            54 => 58,
            62 => 59,
            69 => 60,
            77 => 61,
            84 => 62,
            92 => 63,
            99 => 64,
            107 => 65,
            114 => 66,
            122 => 67,
            130 => 68,
            138 => 69,
            146 => 70,
            154 => 71,
            163 => 72,
            171 => 73,
            180 => 74,
            189 => 75,
            198 => 76,
            207 => 77,
            216 => 78,
            226 => 79,
            236 => 80,
            246 => 81,
            257 => 82,
            268 => 83,
            279 => 84,
            291 => 85,
            303 => 86,
            316 => 87,
            329 => 88,
            345 => 89,
            358 => 90,
            375 => 91,
            392 => 92,
            412 => 93,
            433 => 94,
            457 => 95,
            485 => 96,
            518 => 97,
            560 => 98,
            620 => 99,
            735 => 100,
        ];

        $finalScoreProbability = 50;

        foreach ($ratingDifferences as $difference => $scoreProbability) {
            if ($ratingDifference >= $difference) {
                $finalScoreProbability = $scoreProbability / 100;
            } else {
                break;
            }
        }

        return (float) ($higherRated ? $finalScoreProbability : 1 - $finalScoreProbability);
    }
}
