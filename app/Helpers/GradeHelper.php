<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class GradeHelper
{
    /**
     * Get the grade scale configuration.
     *
     * @param string|null $preset
     * @return array
     */
    public static function getGradeScale(?string $preset = null): array
    {
        $preset = $preset ?: Config::get('grade_scales.default');
        $presets = Config::get('grade_scales.presets', []);
        
        if (isset($presets[$preset])) {
            if (is_string($presets[$preset])) {
                return $presets[$presets[$preset]] ?? [];
            }
            return $presets[$preset];
        }
        
        return $presets['default'] ?? [];
    }
    
    /**
     * Convert numeric grade to letter grade.
     *
     * @param float $score
     * @param string|null $preset
     * @return string
     */
    public static function toLetterGrade(float $score, ?string $preset = null): string
    {
        $scale = self::getGradeScale($preset);
        
        foreach ($scale as $grade => $config) {
            if ($score >= $config['min']) {
                return $grade;
            }
        }
        
        // Return the lowest grade if no match found
        return array_key_last($scale) ?: 'E';
    }
    
    /**
     * Get grade point for a numeric score.
     *
     * @param float $score
     * @param string|null $preset
     * @return float
     */
    public static function toGradePoint(float $score, ?string $preset = null): float
    {
        $letterGrade = self::toLetterGrade($score, $preset);
        $scale = self::getGradeScale($preset);
        
        return $scale[$letterGrade]['point'] ?? 0.0;
    }
    
    /**
     * Get grade description for a numeric score.
     *
     * @param float $score
     * @param string|null $preset
     * @return string
     */
    public static function getGradeDescription(float $score, ?string $preset = null): string
    {
        $letterGrade = self::toLetterGrade($score, $preset);
        $scale = self::getGradeScale($preset);
        
        return $scale[$letterGrade]['description'] ?? '';
    }
    
    /**
     * Calculate GPA from an array of scores and credits.
     *
     * @param array $scores Array of ['score' => float, 'credit' => int]
     * @param string|null $preset
     * @return float
     */
    public static function calculateGPA(array $scores, ?string $preset = null): float
    {
        $totalPoints = 0;
        $totalCredits = 0;
        $rounding = Config::get('grade_scales.gpa.rounding', 2);
        
        foreach ($scores as $item) {
            $credit = $item['credit'] ?? 0;
            $score = $item['score'] ?? 0;
            
            if ($credit > 0) {
                $totalPoints += self::toGradePoint($score, $preset) * $credit;
                $totalCredits += $credit;
            }
        }
        
        if ($totalCredits === 0) {
            return 0.0;
        }
        
        return round($totalPoints / $totalCredits, $rounding);
    }
    
    /**
     * Get all available grade scale presets.
     *
     * @return array
     */
    public static function getAvailablePresets(): array
    {
        $presets = Config::get('grade_scales.presets', []);
        $result = [];
        
        foreach ($presets as $key => $value) {
            if (is_string($value)) {
                $result[$key] = $value;
            } else {
                $result[$key] = $key;
            }
        }
        
        return $result;
    }
    
    /**
     * Validate if a score is passing based on the minimum grade requirement.
     *
     * @param float $score
     * @param string|null $preset
     * @return bool
     */
    public static function isPassingGrade(float $score, ?string $preset = null): bool
    {
        $minGrade = Config::get('grade_scales.gpa.min_grade', 'D');
        $letterGrade = self::toLetterGrade($score, $preset);
        $scale = self::getGradeScale($preset);
        
        if (!isset($scale[$letterGrade]) || !isset($scale[$minGrade])) {
            return false;
        }
        
        return $scale[$letterGrade]['point'] >= $scale[$minGrade]['point'];
    }
}
