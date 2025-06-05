<?php
require_once 'Recipe.php';

class RecipeFormatter {
    public function formatRecipe(string $rawOutput): ?Recipe {
        try {
            // Try to decode the output as JSON
            $data = json_decode($rawOutput, true);
            
            // Check if required fields are present
            if (!$data || !isset($data['naam']) || !isset($data['ingrediënten']) ||
                !isset($data['bereidingstijd']) || !isset($data['stappen']) ||
                !isset($data['moeilijkheidsgraad'])) {
                return null;
            }
            
            // Create a new Recipe object
            return new Recipe(
                $data['naam'],
                $data['ingrediënten'],
                $data['bereidingstijd'],
                $data['stappen'],
                $data['moeilijkheidsgraad']
            );
        } catch (Exception $e) {
            return null;
        }
    }

    public function tryExtractRecipe(string $rawOutput): ?Recipe {
        // First try parsing as JSON
        $recipe = $this->formatRecipe($rawOutput);
        if ($recipe) return $recipe;

        // If that fails, try a less strict method
        $naam = $this->extractName($rawOutput);
        $ingrediënten = $this->extractIngredients($rawOutput);
        
        if ($naam && !empty($ingrediënten)) {
            return new Recipe($naam, $ingrediënten, "Onbekend", [], "Onbekend");
        }
        return null;
    }

    private function extractName($text) {
        // Simple name extraction - looking for text after "Recipe:" or "Name:"
        if (preg_match('/(?:Recipe|Name):\s*([^\n]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractIngredients($text) {
        $ingredients = [];
        // Look for ingredients list after "Ingredients:" or similar
        if (preg_match('/(?:Ingredients|Ingrediënten):\s*([^#]+)/i', $text, $matches)) {
            $ingredientsText = $matches[1];
            // Split by newlines or commas
            $items = preg_split('/[\n,]+/', $ingredientsText);
            foreach ($items as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $ingredients[] = $item;
                }
            }
        }
        return $ingredients;
    }
}

